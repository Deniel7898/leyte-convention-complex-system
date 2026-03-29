<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Units;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\QR_Code;
use App\Models\InventoryHistory;
use App\Models\Service_Record;
use App\Models\User;
use App\Models\ItemDistribution;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class InventoriesController extends Controller
{
    /**
     * LIVE SEARCH
     */
    public function liveSearch(Request $request)
    {
        $searchTerm = $request->input('query', '');
        $categoryFilter = $request->input('category', null);

        $query = Item::with(['category', 'unit', 'inventories.qrCode'])->orderByDesc('id');

        if (!empty($categoryFilter) && strtolower($categoryFilter) !== 'all') {
            $query->whereHas('category', function ($q) use ($categoryFilter) {
                $q->where('id', $categoryFilter);
            });
        }

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhereHas('category', fn($q2) => $q2->where('name', 'like', "%{$searchTerm}%"))
                    ->orWhereHas('unit', fn($q3) => $q3->where('name', 'like', "%{$searchTerm}%"))
                    ->orWhereHas('inventories.qrCode', fn($q4) => $q4->where('code', 'like', "%{$searchTerm}%"));
            });
        }

        $items = $query->get()->map(function ($item) {
            $item->qrCodes = $item->inventories->pluck('qrCode')->filter();
            $item->received_date = $item->inventories->first()?->received_date ?? '--';
            return $item;
        });

        $perPage = 20;
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedItems = new LengthAwarePaginator(
            $currentItems,
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('inventory.inventory.table', ['inventories' => $paginatedItems]);
    }

    /**
     * GET ALL INVENTORIES
     */
    private function getAllInventories()
    {
        return Inventory::with(['item', 'qrCode', 'itemDistributions'])
            ->get()
            ->map(function ($inventory) {
                $inventory->inventory_type = ($inventory->item && $inventory->item->type == 0) ? 'Consumable' : 'Non-Consumable';
                $inventory->item_name = $inventory->item->name ?? '--';
                $inventory->warranty_expires = ($inventory->item && $inventory->item->type == 0) ? '--' : null;
                $inventory->distribution_status = $inventory->itemDistributions->isEmpty()
                    ? 'Available'
                    : $inventory->itemDistributions->last()->status ?? 'Available';
                return $inventory;
            })
            ->sortByDesc('received_date')
            ->values();
    }

    /**
     * PAGINATED INVENTORIES
     */
    private function getInventories($perPage = 20)
    {
        $allItems = Item::with(['category', 'unit', 'inventories.qrCode'])->get()->map(function ($item) {
            $item->inventory_type = $item->type === 'consumable' ? 'Consumable' : 'Non-Consumable';
            $item->item_name = $item->name;
            $item->qrCodes = $item->inventories->pluck('qrCode')->filter();
            $item->received_date = $item->inventories->first()?->received_date ?? '--';
            return $item;
        })->sortByDesc('created_at')->values();

        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $currentItems = $allItems->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentItems,
            $allItems->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );
    }

    /**
     * INDEX
     */
    public function index()
    {
        $inventories = $this->getInventories();
        $categories = Category::all();
        $inventories_table = view('inventory.inventory.table', compact('inventories'))->render();

        return view('inventory.inventory.index', compact('inventories_table', 'categories', 'inventories'));
    }

    /**
     * CREATE
     */
    public function create()
    {
        $categories = Category::all();
        $units = Units::all();
        return view('inventory.inventory.form', compact('categories', 'units'));
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'type' => 'required|in:consumable,non-consumable',
            'unit_id' => 'nullable|integer|exists:units,id',
            'total_stock' => 'required|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'picture' => 'nullable|image|max:2048',
            'received_date' => 'nullable|date',
        ]);

        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $pictureName = time() . '_' . uniqid() . '.' . $picture->getClientOriginalExtension();
            $picture->storeAs('items', $pictureName, 'public');
            $validated['picture'] = 'items/' . $pictureName;
        }

        $item = Item::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'category_id' => $validated['category_id'],
            'unit_id' => $validated['unit_id'] ?? null,
            'total_stock' => $validated['total_stock'],
            'remaining' => $validated['total_stock'],
            'description' => $validated['description'] ?? null,
            'supplier' => $validated['supplier'] ?? null,
            'picture' => $validated['picture'] ?? null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Generate QR codes and inventory records
        $datetime = date('ymd');
        $prefix = strtoupper(substr($item->name, 0, 1));

        if ($item->type === 'non-consumable' && $item->total_stock > 0) {
            $lastQrToday = QR_Code::where('code', 'like', "LCC-{$prefix}{$datetime}-%")->orderByDesc('code')->first();
            $lastSequence = $lastQrToday ? (int) explode('-', $lastQrToday->code)[2] : 0;

            for ($i = 0; $i < $item->total_stock; $i++) {
                $lastSequence++;
                $sequence = str_pad($lastSequence, 3, '0', STR_PAD_LEFT);
                $qrCodeValue = "LCC-{$prefix}{$datetime}-{$sequence}";
                $qrImagePath = 'qrcodes/' . $qrCodeValue . '.svg';

                $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
                $writer = new Writer($renderer);
                Storage::disk('public')->put($qrImagePath, $writer->writeString($qrCodeValue));

                $inventory = Inventory::create([
                    'id' => Str::uuid(),
                    'item_id' => $item->id,
                    'status' => 'available',
                    'received_date' => $request->received_date ?? now(),
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                QR_Code::create([
                    'code' => $qrCodeValue,
                    'qr_picture' => $qrImagePath,
                    'inventory_id' => $inventory->id,
                    'status' => QR_Code::STATUS_USED,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
            // Log history
            InventoryHistory::create([
                'item_id' => $item->id,
                'action' => 'item created',
                'quantity' => $request->total_stock,
                'notes' => $request->description,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

        } elseif ($item->type === 'consumable') {
            $lastQrToday = QR_Code::where('code', 'like', "LCC-{$prefix}{$datetime}-%")->orderByDesc('code')->first();
            $lastSequence = $lastQrToday ? (int) explode('-', $lastQrToday->code)[2] : 0;
            $lastSequence++;
            $sequence = str_pad($lastSequence, 3, '0', STR_PAD_LEFT);
            $qrCodeValue = "LCC-{$prefix}{$datetime}-{$sequence}";
            $qrImagePath = 'qrcodes/' . $qrCodeValue . '.svg';

            $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
            $writer = new Writer($renderer);
            Storage::disk('public')->put($qrImagePath, $writer->writeString($qrCodeValue));

            Inventory::create([
                'id' => Str::uuid(),
                'item_id' => $item->id,
                'status' => 'available',
                'received_date' => $request->received_date ?? now(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            QR_Code::create([
                'code' => $qrCodeValue,
                'qr_picture' => $qrImagePath,
                'inventory_id' => $item->inventories->last()->id ?? null,
                'status' => QR_Code::STATUS_USED,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Log history
            InventoryHistory::create([
                'item_id' => $item->id,
                'action' => 'item created',
                'quantity' => $request->total_stock,
                'notes' => $request->description,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        $inventories = $this->getInventories();
        return response()->json([
            'table_html' => view('inventory.inventory.table', compact('inventories'))->render(),
            'message' => 'Item added successfully'
        ]);
    }

    /**
     * Add Stock
     */
    public function show_stock(Request $request)
    {
        $items = Item::all();
        $selectedItem = $request->item_id ? Item::find($request->item_id) : null;
        return view('inventory.inventory.add_stock_form', compact('items', 'selectedItem'));
    }

    public function getRecentActivities()
    {
        return InventoryHistory::orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    public function getHomeStats()
    {
        return [
            'total_stock' => Item::sum('total_stock'),
            'total_remaining' => Item::sum('remaining'),
        ];
    }

    public function add_stock(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
            'page' => 'nullable|string|in:home,inventory,items',
        ]);

        $item = Item::findOrFail($request->item_id);

        // Increment stock
        $item->increment('total_stock', $request->quantity);
        $item->increment('remaining', $request->quantity);

        // Log history
        InventoryHistory::create([
            'item_id' => $item->id,
            'action' => 'added stock',
            'quantity' => $request->quantity,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Return response depending on page
        if ($request->page === 'items') {
            $item->load('unit', 'category');
            $history = InventoryHistory::where('item_id', $item->id)
                ->with(['creator', 'updater'])
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'item_card_html' => view('inventory.items.item_card', compact('item'))->render(),
                'history_table_html' => view('inventory.items.history_table', compact('item', 'history'))->render(),
                'item_id' => $item->id,
                'message' => 'Stock added successfully',
            ]);

        } elseif ($request->page === 'inventory') {
            $inventories = $this->getInventories(); // Make sure this returns a collection of items
            return response()->json([
                'table_html' => view('inventory.inventory.table', compact('inventories'))->render(),
                'message' => 'Stock added successfully',
            ]);

        } else { // default to 'home'
            $recent_activities = $this->getRecentActivities(); // Should return collection
            $stats = $this->getHomeStats(); // Should return array ['total_stock'=>..., etc.]

            return response()->json([
                'recent_activity_html' => view('home.recent_activity', compact('recent_activities'))->render(),
                'stats_html' => view('home.stats_cards', compact('stats'))->render(),
                'message' => 'Stock added successfully',
            ]);
        }
    }

    /**
     * SHOW
     */
    public function show(string $id)
    {
        $categories = Category::all();
        $units = Units::all();
        $inventory = Inventory::with('item', 'users')->findOrFail($id);
        $inventoryType = ($inventory->item->type == 0) ? 'consumable' : 'non-consumable';

        return view('inventory.items.view', compact('inventory', 'categories', 'units', 'inventoryType'));
    }

    /**
     * EDIT
     */
    public function edit(string $id)
    {
        $categories = Category::all();
        $units = Units::all();
        $item = Item::findOrFail($id);
        $inventory = Inventory::where('item_id', $item->id)->first();

        return view('inventory.inventory.form', compact('item', 'categories', 'units', 'inventory'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'type' => 'nullable|in:consumable,non-consumable',
            'unit_id' => 'nullable|integer|exists:units,id',
            'total_stock' => 'nullable|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'picture' => 'nullable|image|max:2048',
            'received_date' => 'nullable|date',
            'page' => 'nullable|string',
        ]);

        $inventory = Inventory::with('item')->findOrFail($id);
        $item = $inventory->item;

        $item->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'type' => $validated['type'] ?? $item->type,
            'unit_id' => $validated['unit_id'] ?? $item->unit_id,
            'description' => $validated['description'] ?? $item->description,
            'supplier' => $validated['supplier'] ?? $item->supplier,
            'updated_by' => Auth::id(),
        ]);

        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $pictureName = time() . '_' . uniqid() . '.' . $picture->getClientOriginalExtension();
            $picture->storeAs('items', $pictureName, 'public');
            $item->update(['picture' => 'items/' . $pictureName]);
        }

        $inventory->update([
            'received_date' => $validated['received_date'] ?? $inventory->received_date,
            'updated_by' => Auth::id(),
        ]);

        if ($request->page === 'inventory') {

            $inventories = $this->getInventories();
            return response()->json([
                'table_html' => view('inventory.inventory.table', compact('inventories'))->render(),
                'message' => 'Inventory updated successfully'
            ]);
        } elseif ($request->page === 'items') {
            $item->load('unit', 'category');
            $history = InventoryHistory::where('item_id', $item->id)->with(['creator', 'updater'])->orderByDesc('created_at')->get();

            return response()->json([
                'item_card_html' => view('inventory.items.item_card', compact('item'))->render(),
                'item_id' => $item->id,
                'history_table_html' => view('inventory.items.history_table', compact('item', 'history'))->render(),
                'message' => 'Item updated successfully'
            ]);
        }
    }

    /**
     * DELETE
     */
    public function destroy(string $id)
    {
        // Find the inventory record
        $inventory = Inventory::with('item')->findOrFail($id);
        $item = $inventory->item;

        if ($item) {
            // Decrement stock safely, but not below 0
            $item->decrement('total_stock', 1);
            $item->decrement('remaining', 1);
        }

        // Optional: log history for deletion
        if ($item) {
            InventoryHistory::create([
                'item_id' => $item->id,
                'action' => 'deleted',           // mark as deleted
                'quantity' => 1,
                'notes' => 'Non-consumable item deleted',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        // Delete the inventory record
        $inventory->delete();

        // Reload item relations
        $item->load('unit', 'category', 'inventories.qrCode');

        // Get updated history
        $history = InventoryHistory::where('item_id', $item->id)
            ->with(['creator', 'updater'])
            ->orderByDesc('created_at')
            ->get();

        // Render updated non-consumable table
        $nonConsumableTableHtml = view('inventory.items.non_consumable_table', compact('item'))->render();

        // Return updated HTML + message
        return response()->json([
            'item_card_html' => view('inventory.items.item_card', compact('item'))->render(),
            'history_table_html' => view('inventory.items.history_table', compact('item', 'history'))->render(),
            'non_consumable_table_html' => $nonConsumableTableHtml,
            'item_id' => $item->id,
            'message' => 'Inventory deleted successfully'
        ]);
    }
}
