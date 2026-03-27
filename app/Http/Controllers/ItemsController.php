<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Units;
use App\Models\Inventory;
use App\Models\QR_Code;
use App\Models\InventoryHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ItemsController extends Controller
{
    /**
     * Display items
     */
    public function index()
    {
        $items = $this->getItems();

        return view('inventory.items.index', compact('items'));
    }

    /**
     * Live search items
     */
    public function liveSearch(Request $request)
    {
        $searchTerm = $request->input('query', '');
        $statusFilter = $request->input('status', null);
        $categoryFilter = $request->input('category', null);

        $items = $this->getItems();

        if ($searchTerm != '') {
            $searchLower = strtolower($searchTerm);
            $items = $items->filter(function ($item) use ($searchTerm, $searchLower) {
                $match = false;
                if (stripos($item->name, $searchTerm) !== false || stripos($item->description, $searchTerm) !== false) {
                    $match = true;
                }
                if ($item->unit && stripos($item->unit->name, $searchTerm) !== false) {
                    $match = true;
                }
                if ($item->category && stripos($item->category->name, $searchTerm) !== false) {
                    $match = true;
                }
                if (in_array($searchLower, ['non-consumable', 'non']) && $item->type == 1) {
                    $match = true;
                }
                if (in_array($searchLower, ['available', 'avail']) && $item->remaining > 0) {
                    $match = true;
                }
                if (in_array($searchLower, ['not', 'not available']) && $item->remaining == 0) {
                    $match = true;
                }
                return $match;
            });
        }

        if ($statusFilter && strtolower($statusFilter) !== 'all status') {
            $wantAvailable = strtolower($statusFilter) === 'available';
            $items = $items->filter(fn($item) => $wantAvailable ? $item->remaining > 0 : $item->remaining == 0);
        }

        if ($categoryFilter && strtolower($categoryFilter) != 'all') {
            $items = $items->filter(fn($item) => $item->category && $item->category->id == $categoryFilter);
        }

        return view('inventory.items.table', compact('items'));
    }

    /**
     * Get items with remaining inventory
     */
    private function getItems()
    {
        return Item::with(['unit', 'category', 'inventories.qrCode'])->get()->map(function ($item) {
            // Remaining available inventories
            $remaining = $item->inventories
                ->filter(fn($inv) => strtolower($inv->status ?? 'available') === 'available')
                ->count();

            // Count history for non-consumable items
            $historyCount = 0;
            if ($item->type == 1) { // 1 = non-consumable
                $historyCount = InventoryHistory::where('item_id', $item->id)->count();
            }

            return (object) [
                'id' => $item->id,
                'name' => $item->name,
                'remaining' => $remaining,
                'type' => $item->type == 1 ? 'non-consumable' : 'consumable',
                'unit' => $item->unit ?? null,
                'category' => $item->category ?? null,
                'description' => $item->description ?? '--',
                'picture' => $item->picture ?? null,
                'inventories' => $item->inventories,
                'historyCount' => $historyCount, // ✅ always include
            ];
        });
    }

    /**
     * Get inventories for a specific item
     */
    private function getInventories($itemId, $perPage = 20)
    {
        $allInventories = Inventory::with('qrCode')
            ->where('item_id', $itemId)
            ->orderByDesc('created_at')
            ->get();

        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $currentItems = $allInventories->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentItems,
            $allInventories->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );
    }

    /**
     * Show add unit form
     */
    public function create(Request $request)
    {
        $categories = Category::all();
        $units = Units::all();
        $selectedItem = null;

        if ($request->has('item_id')) {
            $selectedItem = Item::with(['unit', 'category'])->find($request->item_id);
        }

        return view('inventory.items.add_unit', compact('categories', 'units', 'selectedItem'));
    }

    /**
     * Store inventory units
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer',
            'notes' => 'nullable|string',
            'received_date' => 'required|date',
            'page' => 'nullable|string',
        ]);

        $item = Item::findOrFail($request->item_id);

        // Use ymd format for QR code sequence
        $datetime = date('ymd');
        $prefix = strtoupper(substr($item->name, 0, 1));

        // Find last QR code for this item and date to continue sequence
        $lastQrToday = QR_Code::where('code', 'like', "LCC-{$prefix}{$datetime}-%")
            ->orderByDesc('code')
            ->first();

        $lastSequence = $lastQrToday ? (int) explode('-', $lastQrToday->code)[2] : 0;

        for ($i = 0; $i < $request->quantity; $i++) {
            $lastSequence++;
            $sequence = str_pad($lastSequence, 3, '0', STR_PAD_LEFT);
            $qrCodeValue = "LCC-{$prefix}{$datetime}-{$sequence}";
            $qrImagePath = 'qrcodes/' . $qrCodeValue . '.svg';

            // Generate QR code SVG
            $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
            $writer = new Writer($renderer);
            Storage::disk('public')->put($qrImagePath, $writer->writeString($qrCodeValue));

            // Create inventory record
            $inventory = Inventory::create([
                'item_id' => $item->id,
                'received_date' => $request->received_date,
                'status' => 'available',
                'holder' => null,
                'notes' => null,
                'date_assigned' => null,
                'due_date' => null,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Create QR code record
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
            'action' => 'added unit',
            'quantity' => $request->quantity,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Update total_stock and remaining
        $item->increment('total_stock', $request->quantity);
        $item->increment('remaining', $request->quantity);

        // Response based on page
        if ($request->page === 'items') {
            $item->load('unit', 'category', 'inventories.qrCode');

            $history = InventoryHistory::where('item_id', $item->id)
                ->with(['creator', 'updater'])
                ->orderByDesc('created_at')
                ->get();

            // Return the non-consumable table partial
            $nonConsumableTableHtml = view('inventory.items.non_consumable_table', compact('item'))->render();

            return response()->json([
                'item_card_html'           => view('inventory.items.item_card', compact('item'))->render(),
                'history_table_html'       => view('inventory.items.history_table', compact('item', 'history'))->render(),
                'non_consumable_table_html' => $nonConsumableTableHtml,
                'item_id'                  => $item->id,
                'message'                  => 'Distribution added successfully'
            ]);
        } else if ($request->page === 'inventory') {
            $inventories = $this->getInventories($item->id);

            return response()->json([
                'html' => view('inventory.inventory.table', compact('inventories'))->render(),
                'message' => 'Inventory added successfully',
            ]);
        }
    }

    /**
     * Show single item
     */
    public function show(string $id)
    {
        $item = Item::with(['category', 'unit', 'inventories.qrCode'])->findOrFail($id);

        // Fetch all inventory history, regardless of type
            $history = InventoryHistory::where('item_id', $item->id)
                ->with(['creator', 'updater', 'inventory.qrCode'])
                ->orderByDesc('created_at')
                ->get();

        $historyCount = $history->count();

        // Attach historyCount to the item for Blade consistency
        $item->historyCount = $historyCount;

        return view('inventory.items.index', compact('item', 'history', 'historyCount'));
    }

    /**
     * Edit item
     */
    public function edit(Request $request, string $id)
    {
        $categories = Category::all();
        $units = Units::all();

        // Fetch the inventory and related item
        $inventory = Inventory::findOrFail($id);
        $item = $inventory->item;

        // Optional: allow selecting a specific item if passed via request
        $selectedItem = $item; // default to the inventory's item
        if ($request->has('item_id')) {
            $selectedItem = Item::with(['unit', 'category'])->find($request->item_id) ?? $item;
        }

        return view('inventory.items.add_unit', compact('inventory', 'item', 'categories', 'units', 'selectedItem'));
    }

    /**
     * Update inventory unit
     */
    public function update(Request $request, string $id)
    {
        // Find the inventory unit
        $inventory = Inventory::findOrFail($id);

        // Validate only the fields that can be updated
        $validated = $request->validate([
            'holder' => 'nullable|string|max:255',
            'received_date' => 'required|date',
            'date_assigned' => 'nullable|date',
            'notes' => 'nullable|string|max:255',
            'item_id' => 'required|exists:items,id',
        ]);

        $validated['updated_by'] = Auth::id();

        // Update the inventory record
        $inventory->update($validated);

        // Load the updated item for response
        $item = $inventory->item;
        $item->load('unit', 'category', 'inventories.qrCode');

        // Load inventory history
        $history = InventoryHistory::where('item_id', $item->id)
            ->with(['creator', 'updater'])
            ->orderByDesc('created_at')
            ->get();

        // Return JSON to update the modal/partial via AJAX
        return response()->json([
            'item_card_html'            => view('inventory.items.item_card', compact('item'))->render(),
            'history_table_html'        => view('inventory.items.history_table', compact('item', 'history'))->render(),
            'non_consumable_table_html' => view('inventory.items.non_consumable_table', compact('item'))->render(),
            'inventory_id'              => $inventory->id,
            'message'                   => 'Inventory unit updated successfully',
        ]);
    }

    /**
     * Delete item
     */
    public function destroy(string $id)
    {
        // Find the item and delete it
        $item = Item::findOrFail($id);
        $item->delete();

        // Return JSON with redirect only
        return response()->json([
            'message' => 'Item deleted successfully',
            'redirect' => route('inventory.index'), // inventory page route
        ]);
    }
}
