<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Units;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\QR_Code;
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
        $statusFilter = $request->input('status', null);
        $categoryFilter = $request->input('category', null);

        $inventories = $this->getAllInventories();

        if (!empty($searchTerm)) {

            $inventories = $inventories->filter(function ($inventory) use ($searchTerm) {

                if (!$inventory->item) return false;

                $match = false;

                if (stripos($inventory->item->name, $searchTerm) !== false)
                    $match = true;

                if ($inventory->item->unit && stripos($inventory->item->unit->name, $searchTerm) !== false)
                    $match = true;

                if ($inventory->item->category && stripos($inventory->item->category->name, $searchTerm) !== false)
                    $match = true;

                if (!empty($inventory->qrCode->code) && stripos($inventory->qrCode->code, $searchTerm) !== false)
                    $match = true;

                return $match;
            });
        }

        if (!empty($categoryFilter) && strtolower($categoryFilter) !== 'all') {

            $inventories = $inventories->filter(function ($inventory) use ($categoryFilter) {

                return $inventory->item
                    && $inventory->item->category
                    && $inventory->item->category->id == $categoryFilter;
            });
        }

        $inventories = $inventories->values();

        $perPage = 20;
        $currentPage = Paginator::resolveCurrentPage() ?: 1;

        $currentItems = $inventories
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values();

        $paginatedInventories = new LengthAwarePaginator(
            $currentItems,
            $inventories->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('inventory.inventory.table', [
            'inventories' => $paginatedInventories
        ]);
    }


    /**
     * GET ALL INVENTORIES
     */
    private function getAllInventories()
    {
        return Inventory::with(['item', 'qrCode', 'itemDistributions'])
            ->get()
            ->map(function ($inventory) {

                $inventory->inventory_type =
                    $inventory->item && $inventory->item->type == 0
                    ? 'Consumable'
                    : 'Non-Consumable';

                $inventory->item_name = $inventory->item->name ?? '--';

                if ($inventory->item && $inventory->item->type == 0) {
                    $inventory->warranty_expires = '--';
                }

                $inventory->distribution_status =
                    $inventory->itemDistributions->isEmpty()
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

        $allInventories = $this->getAllInventories();

        $currentPage = Paginator::resolveCurrentPage() ?: 1;

        $currentItems = $allInventories
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values();

        return new LengthAwarePaginator(
            $currentItems,
            $allInventories->count(),
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

        $inventories_table = view(
            'inventory.inventory.table',
            compact('inventories')
        )->render();

        return view(
            'inventory.inventory.index',
            compact('inventories_table', 'categories', 'inventories')
        );
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
            'category_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'unit_id' => 'required|integer',
            'description' => 'nullable|string',
            'picture' => 'nullable|image|max:2048',
            'received_date' => 'nullable|date',
            'warranty_expires' => 'nullable|date',
        ]);

        if ($request->hasFile('picture')) {

            $picture = $request->file('picture');

            $pictureName =
                time() . '_' . uniqid() . '.' .
                $picture->getClientOriginalExtension();

            $picture->storeAs('items', $pictureName, 'public');

            $validated['picture'] = 'items/' . $pictureName;
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $item = Item::create($validated);

        $datetime = date('Ymd');
        $prefix = strtoupper(substr($item->name, 0, 1));

        for ($i = 0; $i < $item->quantity; $i++) {

            $sequence = str_pad($i + 1, 3, '0', STR_PAD_LEFT);

            $qrCodeValue = "LCC-{$prefix}{$datetime}-{$sequence}";

            $qrImageName = $qrCodeValue . '.svg';
            $qrImagePath = 'qrcodes/' . $qrImageName;

            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );

            $writer = new Writer($renderer);

            Storage::disk('public')
                ->put($qrImagePath, $writer->writeString($qrCodeValue));

            $inventory = Inventory::create([
                'id' => Str::uuid(),
                'item_id' => $item->id,
                'received_date' => $request->received_date,
                'warranty_expires' =>
                $item->type == 1
                    ? $request->warranty_expires
                    : null,
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

        $inventories = $this->getInventories();

        return response()->json([
            'html' => view('inventory.inventory.table', compact('inventories'))->render(),
            'message' => 'Item added successfully'
        ]);
    }


    /**
     * SHOW
     */
    public function show(string $id)
    {

        $categories = Category::all();
        $units = Units::all();

        $inventory = Inventory::with('item', 'users')->findOrFail($id);

        $inventoryType =
            $inventory->item->type == 0
            ? 'consumable'
            : 'non-consumable';

        return view(
            'inventory.inventory.view',
            compact('inventory', 'categories', 'units', 'inventoryType')
        );
    }


    /**
     * EDIT
     */
    public function edit($id)
    {

        $categories = Category::all();
        $units = Units::all();

        $inventory = Inventory::with('item')->findOrFail($id);

        $inventoryType =
            $inventory->item->type == 0
            ? 'consumable'
            : 'non-consumable';

        return view(
            'inventory.inventory.form',
            compact('inventory', 'categories', 'units', 'inventoryType')
        );
    }


    /**
     * UPDATE
     */
    public function update(Request $request, string $id)
    {

        $validated = $request->validate([
            'received_date' => 'nullable|date',
            'warranty_expires' => 'nullable|date',
        ]);

        $inventory = Inventory::with('item')->findOrFail($id);

        $inventory->update([
            'received_date' => $validated['received_date'] ?? null,
            'warranty_expires' =>
            $inventory->item->type == 1
                ? $validated['warranty_expires']
                : null,
            'updated_by' => Auth::id(),
        ]);

        $inventories = $this->getInventories();

        return response()->json([
            'html' => view('inventory.inventory.table', compact('inventories'))->render(),
            'message' => 'Inventory updated successfully',
        ]);
    }


    /**
     * DELETE
     */
    public function destroy(string $id)
    {

        $inventory = Inventory::findOrFail($id);

        $itemId = $inventory->item_id;

        $inventory->delete();

        $item = Item::find($itemId);

        if ($item && $item->quantity > 0) {
            $item->decrement('quantity');
        }

        $inventories = $this->getInventories();

        return response()->json([
            'html' => view('inventory.inventory.table', compact('inventories'))->render(),
            'message' => 'Inventory deleted successfully',
        ]);
    }
}
