<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Units;
use App\Models\InventoryConsumable;
use App\Models\InventoryNonConsumable;
use Illuminate\Support\Facades\Auth;

class ItemsController extends Controller
{
    public function liveSearch(Request $request)
    {
        $searchTerm = $request->input('query', '');
        $typeFilter = $request->input('type', null);
        $availabilityFilter = $request->input('availability', null);
        $categoryFilter = $request->input('category', null);

        // Use getItems() helper to fetch all items with remaining, unit, category
        $items = $this->getItems();

        // Apply search filters on the collection
        if ($searchTerm != '') {
            $searchLower = strtolower($searchTerm);

            $items = $items->filter(function ($item) use ($searchTerm, $searchLower) {
                $match = false;

                // Text search in name or description
                if (stripos($item->name, $searchTerm) !== false || stripos($item->description, $searchTerm) !== false) {
                    $match = true;
                }

                // Numeric search in quantity
                if (is_numeric($searchTerm) && $item->quantity == $searchTerm) {
                    $match = true;
                }

                // Search in unit name
                if ($item->unit && stripos($item->unit->name, $searchTerm) !== false) {
                    $match = true;
                }

                // Search in category name
                if ($item->category && stripos($item->category->name, $searchTerm) !== false) {
                    $match = true;
                }

                // Type mapping keywords
                if (in_array($searchLower, ['consumable', 'con']) && $item->type == 0) {
                    $match = true;
                }
                if (in_array($searchLower, ['non-consumable', 'non', 'non consumable']) && $item->type == 1) {
                    $match = true;
                }

                // Availability mapping keywords
                if (in_array($searchLower, ['available', 'avail']) && $item->availability == 1) {
                    $match = true;
                }
                if (in_array($searchLower, ['not-available', 'not available', 'not']) && $item->availability == 0) {
                    $match = true;
                }

                return $match;
            });
        }

        // Apply type filter (dropdown)
        if ($typeFilter && strtolower($typeFilter) != 'all') {
            $items = $items->filter(function ($item) use ($typeFilter) {
                if (strtolower($typeFilter) === 'consumable') return $item->type == 0;
                if (in_array(strtolower($typeFilter), ['non-consumable', 'non'])) return $item->type == 1;
                return true;
            });
        }

        // Apply availability filter (dropdown)
        if ($availabilityFilter && strtolower($availabilityFilter) != 'all') {
            $items = $items->filter(function ($item) use ($availabilityFilter) {
                if (strtolower($availabilityFilter) === 'available') return $item->availability == 1;
                if (strtolower($availabilityFilter) === 'not available') return $item->availability == 0;
                return true;
            });
        }

        // Apply category filter (dropdown)
        if ($categoryFilter && strtolower($categoryFilter) != 'all') {
            $items = $items->filter(function ($item) use ($categoryFilter) {
                return $item->category && $item->category->id == $categoryFilter;
            });
        }

        // Reset keys after filtering
        $items = $items->values();

        return view('inventory.items.table', compact('items'));
    }

    /**
     * Helper: get items with remaining, unit, category
     */
    private function getItems()
    {
        return Item::with(['unit', 'category'])->get()->map(function ($item) {

            $remaining = $item->type == 0
                ? InventoryConsumable::where('item_id', $item->id)->count()
                : InventoryNonConsumable::where('item_id', $item->id)->count();

            return (object)[
                'id' => $item->id,
                'name' => $item->name,
                'type' => $item->type,
                'availability' => $item->availability,
                'quantity' => $item->quantity,
                'remaining' => $remaining,
                'unit' => $item->unit ?? null,
                'category' => $item->category ?? null,
                'description' => $item->description ?? '--',
                'picture' => $item->picture,
            ];
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = $this->getItems();
        $categories = Category::all();
        $items_table = view('inventory.items.table', compact('items'))->render();
        return view('inventory.items.index', compact('items_table', 'items', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $units = Units::all();
        return view('inventory.items.form', compact('categories', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|integer|in:0,1',
            'category_id' => 'required|integer',
            'quantity' => 'required|integer',
            'unit_id' => 'required|integer',
            'description' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'warranty_expires' => 'nullable|date', // For non-consumable
        ]);

        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $pictureName = time() . '_' . uniqid() . '.' . $picture->getClientOriginalExtension();
            $picture->storeAs('items', $pictureName, 'public');
            $validated['picture'] = 'items/' . $pictureName;
        }

        $validated['availability'] = 1;
        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $item = Item::create($validated);

        // Create inventory records
        for ($i = 0; $i < $item->quantity; $i++) {
            if ($item->type == 0) {
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

        $items = $this->getItems(); // Use helper to recalc remaining

        return response()->json([
            'html' => view('inventory.items.table', compact('items'))->render(),
            'message' => 'Item added successfully',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $categories = Category::all();
        $units = Units::all();
        $item = Item::findOrFail($id);
        return view('inventory.items.form', compact('item', 'categories', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|integer|in:0,1',
            'category_id' => 'required|integer',
            'quantity' => 'required|integer',
            'unit_id' => 'required|integer',
            'description' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'warranty_expires' => 'nullable|date',
        ]);

        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $pictureName = time() . '_' . uniqid() . '.' . $picture->getClientOriginalExtension();
            $picture->storeAs('items', $pictureName, 'public');
            $validated['picture'] = 'items/' . $pictureName;
        }

        $validated['updated_by'] = Auth::id();

        $item = Item::findOrFail($id);
        $item->update($validated);

        $items = $this->getItems(); // Recalculate remaining

        return response()->json([
            'html' => view('inventory.items.table', compact('items'))->render(),
            'message' => 'Item updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Item::findOrFail($id);
        $item->delete();

        $items = $this->getItems(); // Recalculate remaining

        return response()->json([
            'html' => view('inventory.items.table', compact('items'))->render(),
        ]);
    }
}
