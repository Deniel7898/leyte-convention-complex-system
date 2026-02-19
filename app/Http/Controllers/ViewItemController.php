<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Units;
use App\Models\Category;
use App\Models\InventoryNonConsumable;
use App\Models\InventoryConsumable;
use Illuminate\Support\Facades\Auth;

class ViewItemController extends Controller
{
    private function getInventories($itemId)
    {
        $consumables = InventoryConsumable::with(['item', 'qr_code'])
            ->where('item_id', $itemId)
            ->get()
            ->map(function ($c) {
                $c->inventory_type = 'Consumable';
                $c->warranty_expires = '--';

                return $c;
            });

        $nonConsumables = InventoryNonConsumable::with(['item', 'qr_code'])
            ->where('item_id', $itemId)
            ->get()
            ->map(function ($n) {
                $n->inventory_type = 'Non-Consumable';
                $n->warranty_expires = $n->warranty_expires ?? '--';

                return $n;
            });

        return $consumables->merge($nonConsumables)
            ->sortByDesc('received_date')
            ->values();
    }

    /**
     * Show the form for creating a new inventory
     */
    public function create($itemId = null)
    {
        $categories = Category::all();
        $units = Units::all();
        $items = Item::all();

        // Always set selectedItem
        if ($itemId) {
            $selectedItem = Item::findOrFail($itemId);
        } else {
            $selectedItem = $items->first(); // default item for Add
        }

        return view('inventory.viewItem.form', compact('items', 'selectedItem', 'categories', 'units'));
    }

    /**
     * Store a new inventory
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'type' => 'required|in:0,1',
            'received_date' => 'required|date',
            'warranty_expires' => 'nullable|date',
        ]);

        $item = Item::findOrFail($request->item_id);

        // Increment quantity
        $item->increment('quantity');

        if ($item->type == 0) {
            InventoryConsumable::create([
                'item_id' => $item->id,
                'received_date' => $request->received_date,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        } else {
            InventoryNonConsumable::create([
                'item_id' => $item->id,
                'received_date' => $request->received_date,
                'warranty_expires' => $request->warranty_expires,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

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
        $selectedItem = $inventory->item; // For dropdown defaults

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
