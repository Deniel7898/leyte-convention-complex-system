<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Units;
use App\Models\Item;
use App\Models\InventoryConsumable;
use App\Models\InventoryNonConsumable;
use Illuminate\Support\Facades\Auth;

class InventoriesController extends Controller
{
    /**
     * Live Search for Inventory
     */
    public function liveSearch(Request $request)
    {
        $searchTerm = $request->input('query', '');
        $typeFilter = $request->input('type', null);
        $statusFilter = $request->input('status', null);
        $categoryFilter = $request->input('category', null);

        $inventories = $this->getInventories();

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
                    !empty($inventory->receive_date) &&
                    stripos($inventory->receive_date, $searchTerm) !== false
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

                // Status keywords
                if (in_array($searchLower, ['available', 'avail']) && $inventory->item->status == 1) {
                    $match = true;
                }
                if (in_array($searchLower, ['not available', 'not-available', 'not']) && $inventory->item->status == 0) {
                    $match = true;
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

        // Apply type filter
        if ($typeFilter && strtolower($typeFilter) != 'all') {
            $inventories = $inventories->filter(function ($inventory) use ($typeFilter) {
                if (strtolower($typeFilter) === 'consumable') return $inventory->item->type == 0;
                if (in_array(strtolower($typeFilter), ['non-consumable', 'non'])) return $inventory->item->type == 1;
                return true;
            });
        }

        // Apply status filter
        if ($statusFilter && strtolower($statusFilter) != 'all') {
            $inventories = $inventories->filter(function ($inventory) use ($statusFilter) {
                if (strtolower($statusFilter) === 'available') return $inventory->item->status == 1;
                if (strtolower($statusFilter) === 'not available') return $inventory->item->status == 0;
                return true;
            });
        }

        // Apply category filter
        if ($categoryFilter && strtolower($categoryFilter) != 'all') {
            $inventories = $inventories->filter(function ($inventory) use ($categoryFilter) {
                return $inventory->item->category && $inventory->item->category->id == $categoryFilter;
            });
        }

        // Reset keys after filtering
        $inventories = $inventories->values();

        return view('inventory.inventory.table', compact('inventories'));
    }

    /**
     * Helper: Get all inventories (Consumable + Non-Consumable)
     */
    private function getInventories()
    {
        $consumables = InventoryConsumable::with(['item', 'qr_code'])
            ->get()
            ->map(function ($c) {
                $c->inventory_type = 'Consumable';
                $c->warranty_expires = '--'; // consumables don't have warranty
                return $c;
            });

        $nonConsumables = InventoryNonConsumable::with(['item', 'qr_code'])
            ->get()
            ->map(function ($n) {
                $n->inventory_type = 'Non-Consumable';
                $n->warranty_expires = $n->warranty_expires ?? '--';
                return $n;
            });

        return $consumables
            ->merge($nonConsumables)
            ->sortByDesc('receive_date')
            ->values();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all inventories (consumables + non-consumables)
        $inventories = $this->getInventories();

        // Get all categories for filter dropdowns
        $categories = Category::all();

        // Render the inventory table partial
        $inventories_table = view('inventory.inventory.table', compact('inventories'))->render();

        // Return the main index view
        return view('inventory.inventory.index', compact('inventories_table', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $units = Units::all();
        return view('inventory.inventory.form', compact('categories', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|integer|in:0,1',
            'category_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'unit_id' => 'required|integer',
            'status' => 'required|integer|in:0,1',
            'description' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Handle picture upload
        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $pictureName = time() . '_' . uniqid() . '.' . $picture->getClientOriginalExtension();
            $picture->storeAs('items', $pictureName, 'public');
            $validated['picture'] = 'items/' . $pictureName;
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        // Save the new item and get it in a variable
        $item = Item::create($validated);

        // Loop only for the quantity of the newly added item
        for ($i = 0; $i < $item->quantity; $i++) {
            if ((int) $item->type === 0) {
                InventoryConsumable::create([
                    'item_id' => $item->id,
                    'receive_date' => now(),
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            } else {
                InventoryNonConsumable::create([
                    'item_id' => $item->id,
                    'warranty_expires' => $request->warranty_expires,
                    'receive_date' => now(),
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
        }

        // Get all inventories (consumables + non-consumables)
        $inventories = $this->getInventories();

        // Return the table partial with inventories
        return response()->json([
            'html' => view('inventory.inventory.table', compact('inventories'))->render(),
            'message' => 'Item added successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $categories = Category::all();
        $units = Units::all();

        try {
            $inventory = InventoryConsumable::with('item')->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $inventory = InventoryNonConsumable::with('item')->findOrFail($id);
        }

        return view('inventory.inventory.form', compact('inventory', 'categories', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|integer|in:0,1',
            'warranty_expires' => 'nullable|date',
        ]);

        // Try to find in consumables first
        try {
            $inventory = InventoryConsumable::with('item')->findOrFail($id);
            $isNonConsumable = false;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $inventory = InventoryNonConsumable::with('item')->findOrFail($id);
            $isNonConsumable = true;
        }

        // ✅ Update Item status (because status is from items table)
        $inventory->item->update([
            'status' => $validated['status'],
            'updated_by' => Auth::id(),
        ]);

        // ✅ If Non-Consumable, update warranty
        if ($isNonConsumable) {
            $inventory->update([
                'warranty_expires' => $validated['warranty_expires'] ?? null,
                'updated_by' => Auth::id(),
            ]);
        }

        // Get all inventories (consumables + non-consumables)
        $inventories = $this->getInventories();

        return response()->json([
            'html' => view('inventory.inventory.table', compact('inventories'))->render(),
            'message' => 'Inventory updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Try to find in consumables first
        $inventoryConsumable = InventoryConsumable::find($id);
        $inventoryNonConsumable = InventoryNonConsumable::find($id);

        if ($inventoryConsumable) {
            $inventoryConsumable->delete();
        } elseif ($inventoryNonConsumable) {
            $inventoryNonConsumable->delete();
        } else {
            return response()->json(['error' => 'Inventory not found'], 404);
        }

        // Get all inventories (consumables + non-consumables)
        $inventories = $this->getInventories();

        return response()->json([
            'html' => view('inventory.inventory.table', compact('inventories'))->render(),
            'message' => 'Inventory deleted successfully',
        ]);
    }
}
