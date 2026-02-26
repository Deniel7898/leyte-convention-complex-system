<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Units;
use App\Models\Category;
use App\Models\InventoryNonConsumable;
use App\Models\InventoryConsumable;
use App\Models\QR_Code;
use Illuminate\Support\Facades\Auth;

class ViewItemController extends Controller
{
    /**
     * Live Search for Inventory
     */
    public function liveSearch(Request $request)
    {
        $searchTerm = $request->input('query', '');
        $statusFilter = $request->input('status', null);
        $itemId = $request->input('item_id');

        $inventories = $this->getInventories($itemId);

        // Apply text search
        if ($searchTerm != '') {
            $searchLower = strtolower($searchTerm);

            $inventories = $inventories->filter(function ($inventory) use ($searchTerm, $searchLower) {

                if (!$inventory->item) return false;

                $match = false;

                // Search item name
                if (stripos($inventory->item->name, $searchTerm) !== false) {
                    $match = true;
                }

                // Search received date
                if (
                    !empty($inventory->received_date) &&
                    stripos($inventory->received_date, $searchTerm) !== false
                ) {
                    $match = true;
                }

                // Search warranty date
                if (
                    !empty($inventory->warranty_expires) &&
                    stripos($inventory->warranty_expires, $searchTerm) !== false
                ) {
                    $match = true;
                }

                // Type keywords
                if (in_array($searchLower, ['consumable', 'con']) && $inventory->item->type == 0) {
                    $match = true;
                }
                if (in_array($searchLower, ['non-consumable', 'non', 'non consumable']) && $inventory->item->type == 1) {
                    $match = true;
                }

                // Status keywords (from distribution)
                if (isset($inventory->distribution_status)) {
                    if (in_array($searchLower, ['available', 'avail']) && strtolower($inventory->distribution_status) === 'available') {
                        $match = true;
                    }

                    if (
                        in_array($searchLower, ['distributed', 'borrowed', 'pending', 'partial', 'returned', 'received']) &&
                        strtolower($inventory->distribution_status) === $searchLower
                    ) {
                        $match = true;
                    }
                }

                // Search in unit name
                if (
                    $inventory->item->unit &&
                    stripos($inventory->item->unit->name, $searchTerm) !== false
                ) {
                    $match = true;
                }

                // Search in category name
                if (
                    $inventory->item->category &&
                    stripos($inventory->item->category->name, $searchTerm) !== false
                ) {
                    $match = true;
                }

                return $match;
            });
        }

        // Apply status filter
        $statusFilterLower = strtolower($statusFilter ?? '');

        if (!in_array($statusFilterLower, ['all', 'all status']) && $statusFilterLower !== '') {
            $inventories = $inventories->filter(
                fn($inventory) =>
                isset($inventory->distribution_status) &&
                    strtolower($inventory->distribution_status) === $statusFilterLower
            );
        }

        // Reset keys after filtering
        $viewItems = $inventories->values();

        return view('inventory.viewItem.table', compact('viewItems'));
    }

    /**
     * Helper: Get all inventories (Consumable + Non-Consumable)
     */
    private function getInventories($itemId)
    {
        $consumables = InventoryConsumable::with(['item', 'qrCode', 'itemDistributions'])
            ->where('item_id', $itemId)
            ->get()
            ->map(function ($c) {
                $c->inventory_type = 'Consumable';
                $c->warranty_expires = '--';

                // Determine status from distributions
                if ($c->itemDistributions->isEmpty()) {
                    $c->distribution_status = 'Available';
                } else {
                    // Get the latest distribution status
                    $c->distribution_status = $c->itemDistributions->last()->status ?? 'Available';
                }

                return $c;
            });

        $nonConsumables = InventoryNonConsumable::with(['item', 'qrCode', 'itemDistributions'])
            ->where('item_id', $itemId)
            ->get()
            ->map(function ($n) {
                $n->inventory_type = 'Non-Consumable';
                $n->warranty_expires = $n->warranty_expires ?? '--';

                if ($n->itemDistributions->isEmpty()) {
                    $n->distribution_status = 'Available';
                } else {
                    $n->distribution_status = $n->itemDistributions->last()->status ?? 'Available';
                }

                return $n;
            });

        return $consumables->merge($nonConsumables)
            ->sortByDesc('received_date')
            ->values();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new inventory
     */
    public function create($item = null)
    {
        $categories = Category::all();
        $units = Units::all();
        $items = Item::all();

        // Use the passed item ID or fallback
        $selectedItem = $item ? Item::findOrFail($item) : $items->first();

        return view('inventory.viewItem.form', compact('items', 'selectedItem', 'categories', 'units'));
    }

    /**
     * Store a new inventory
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:0,1',
            'received_date' => 'required|date',
            'warranty_expires' => 'nullable|date',
        ]);

        $item = Item::findOrFail($request->item_id);

        // Current date for today
        $datetime = date('Ymd'); // e.g., 20260225
        $prefix = strtoupper(substr($item->name, 0, 1));

        // Get the last QR code for today (sequence resets daily)
        $lastQrToday = QR_Code::where('code', 'like', "LCC-{$prefix}{$datetime}-%")
            ->orderByDesc('code')
            ->first();

        $lastSequence = 0;
        if ($lastQrToday) {
            $parts = explode('-', $lastQrToday->code);
            $lastSequence = (int) end($parts);
        }

        // Loop to create inventory records & QR codes based on quantity
        for ($i = 0; $i < $request->quantity; $i++) {

            $lastSequence++;
            $sequence = str_pad($lastSequence, 3, '0', STR_PAD_LEFT);
            $qrCodeValue = 'LCC-' . $prefix . $datetime . '-' . $sequence;

            if ($item->type == 0) {
                // Create consumable inventory
                $consumable = InventoryConsumable::create([
                    'item_id' => $item->id,
                    'received_date' => $request->received_date,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                // Create QR code linked to this consumable
                QR_Code::create([
                    'code' => $qrCodeValue,
                    'inventory_consumable_id' => $consumable->id,
                    'status' => QR_Code::STATUS_USED,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            } else {
                // Create non-consumable inventory
                $nonConsumable = InventoryNonConsumable::create([
                    'item_id' => $item->id,
                    'received_date' => $request->received_date,
                    'warranty_expires' => $request->warranty_expires,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                // Create QR code linked to this non-consumable
                QR_Code::create([
                    'code' => $qrCodeValue,
                    'inventory_non_consumable_id' => $nonConsumable->id,
                    'status' => QR_Code::STATUS_USED,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
        }

        // Optionally increment total quantity in items table
        $item->increment('quantity', $request->quantity);

        $viewItems = $this->getInventories($item->id);

        return response()->json([
            'html' => view('inventory.viewItem.table', compact('viewItems'))->render(),
            'message' => 'Inventory added successfully',
        ]);
    }

    /**
     * Show the inventory table for a specific item
     */
    public function show($id)
    {
        $viewItem = Item::with(['category', 'unit'])->findOrFail($id);

        $viewItems = $this->getInventories($viewItem->id);

        $viewItems_table = view('inventory.viewItem.table', compact('viewItems', 'viewItem'))->render();

        return view('inventory.viewItem.index', compact('viewItem', 'viewItems', 'viewItems_table'));
    }

    /**
     * Show the form for editing an inventory
     */
    public function edit($id)
    {
        // Find inventory item (non-consumable first)
        $inventory = InventoryNonConsumable::with('item')->find($id);

        if (!$inventory) {
            $inventory = InventoryConsumable::with('item')->findOrFail($id);
        }

        $items = Item::all();
        $categories = Category::all();
        $units = Units::all();

        $item = $inventory; // Use $item for the form action
        $selectedItem = $inventory->item; // For Item input Read only

        return view('inventory.viewItem.form', compact('items', 'item', 'selectedItem', 'categories', 'units'));
    }

    /**
     * Update an inventory
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'received_date' => 'required|date',
            'warranty_expires' => 'nullable|date',
        ]);

        // Find the inventory record (non‑consumable first)
        $inventory = InventoryNonConsumable::find($id);

        if ($inventory) {
            // Non‑consumable
            $inventory->warranty_expires = $request->warranty_expires;
        } else {
            // Consumable
            $inventory = InventoryConsumable::findOrFail($id);
        }

        // Update common fields
        $inventory->item_id = $request->item_id;
        $inventory->received_date = $request->received_date;
        $inventory->updated_by = Auth::id();

        // Save the changes
        $inventory->save();

        // Now pass the item id
        $viewItems = $this->getInventories($inventory->item_id);

        return response()->json([
            'html' => view('inventory.viewItem.table', compact('viewItems'))->render(),
            'message' => 'Item updated successfully',
        ]);
    }


    /**
     * Delete an inventory
     */
    public function destroy($id)
    {
        // Try to find the inventory (non-consumable first)
        $inventory = InventoryNonConsumable::find($id);

        if ($inventory) {
            $itemId = $inventory->item_id;
            $inventory->delete();
        } else {
            $inventory = InventoryConsumable::findOrFail($id);
            $itemId = $inventory->item_id;
            $inventory->delete();
        }

        // Decrement item quantity safely
        $item = Item::find($itemId);
        if ($item && $item->quantity > 0) {
            $item->decrement('quantity'); // minus 1
        }

        // Get updated inventories
        $viewItems = $this->getInventories($itemId);

        return response()->json([
            'html' => view('inventory.viewItem.table', compact('viewItems'))->render(),
            'message' => 'Inventory deleted successfully',
        ]);
    }
}
