<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\InventoryConsumable;
use App\Models\InventoryNonConsumable;
use App\Models\ItemDistribution;
use App\Models\QR_Code;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ItemDistributionsController extends Controller
{
    /**
     * Live Search for Item Distributions
     */
    public function liveSearch(Request $request)
    {
        $searchTerm     = $request->input('query', '');
        $typeFilter     = $request->input('type', null);
        $statusFilter   = $request->input('status', null);
        $categoryFilter = $request->input('category', null);
        $distTypeFilter = $request->input('dist_type', null);

        $distributions = $this->getItemDistributions();

        // TEXT SEARCH 
        if ($searchTerm != '') {

            $searchLower = strtolower($searchTerm);

            $distributions = $distributions->filter(function ($dist) use ($searchTerm, $searchLower) {

                if (!$dist->item) return false;

                $match = false;

                // Search item name
                if (stripos($dist->item->name, $searchTerm) !== false) {
                    $match = true;
                }

                // Search unit
                if (
                    $dist->item->unit &&
                    stripos($dist->item->unit->name, $searchTerm) !== false
                ) {
                    $match = true;
                }

                // Search category
                if (
                    $dist->item->category &&
                    stripos($dist->item->category->name, $searchTerm) !== false
                ) {
                    $match = true;
                }

                // Search QR Code
                if (
                    $dist->qrCode &&
                    stripos($dist->qrCode->code, $searchTerm) !== false
                ) {
                    $match = true;
                }

                // Search status
                if (
                    $dist->status &&
                    stripos($dist->status, $searchTerm) !== false
                ) {
                    $match = true;
                }

                // Search description
                if (
                    $dist->description &&
                    stripos($dist->description, $searchTerm) !== false
                ) {
                    $match = true;
                }

                // Search remarks
                if (
                    $dist->remarks &&
                    stripos($dist->remarks, $searchTerm) !== false
                ) {
                    $match = true;
                }

                // Type keywords
                if (in_array($searchLower, ['consumable', 'con']) && $dist->item->type == 0) {
                    $match = true;
                }

                if (in_array($searchLower, ['non-consumable', 'non', 'non consumable']) && $dist->item->type == 1) {
                    $match = true;
                }

                // Distribution type keywords
                if (in_array($searchLower, ['distribution']) && $dist->type == 0) {
                    $match = true;
                }

                if (in_array($searchLower, ['borrow', 'borrowed']) && $dist->type == 1) {
                    $match = true;
                }

                // Search Distribution date
                if (!empty($dist->distribution_date) && $dist->distribution_date != '--') {
                    try {
                        $formattedReceived = Carbon::parse($dist->distribution_date)->format('M d, Y');
                        if (stripos($formattedReceived, $searchTerm) !== false) {
                            $match = true;
                        }
                    } catch (\Exception $e) {
                        // Ignore invalid dates
                    }
                }

                // Search Due date
                if (!empty($dist->due_date) && $dist->due_date != '--') {
                    try {
                        $formattedReceived = Carbon::parse($dist->due_date)->format('M d, Y');
                        if (stripos($formattedReceived, $searchTerm) !== false) {
                            $match = true;
                        }
                    } catch (\Exception $e) {
                        // Ignore invalid dates
                    }
                }

                return $match;
            });
        }

        //TYPE FILTER (Consumable / Non-Consumable)
        if (!empty($typeFilter) && !str_contains(strtolower($typeFilter), 'all')) {

            $distributions = $distributions->filter(function ($dist) use ($typeFilter) {

                if (!$dist->item) return false;

                if (strtolower($typeFilter) === 'consumable') {
                    return $dist->item->type == 0;
                }

                if (strtolower($typeFilter) === 'non-consumable') {
                    return $dist->item->type == 1;
                }

                return true;
            });
        }

        // STATUS FILTER
        if (!empty($statusFilter) && !str_contains(strtolower($statusFilter), 'all')) {

            $statusLower = strtolower($statusFilter);

            $distributions = $distributions->filter(function ($dist) use ($statusLower) {
                return strtolower($dist->status) === $statusLower;
            });
        }

        // CATEGORY FILTER
        if (!empty($categoryFilter) && strtolower($categoryFilter) !== 'all') {

            $distributions = $distributions->filter(function ($dist) use ($categoryFilter) {

                if (!$dist->item || !$dist->item->category) return false;

                return $dist->item->category->id == $categoryFilter;
            });
        }

        // DISTRIBUTION TYPE FILTER
        if (!empty($distTypeFilter) && !str_contains(strtolower($distTypeFilter), 'all')) {

            $distributions = $distributions->filter(function ($dist) use ($distTypeFilter) {

                if (strtolower($distTypeFilter) === 'distribution') {
                    return $dist->type == 0;
                }

                if (strtolower($distTypeFilter) === 'borrow') {
                    return $dist->type == 1;
                }

                return true;
            });
        }

        $itemDistributions = $distributions->values();

        return view('item_distributions.table', compact('itemDistributions'));
    }

    /**
     * Helper: Get all Item Distributions (Consumable + Non-Consumable)
     */
    private function getItemDistributions()
    {
        $consumables = ItemDistribution::with([
            'inventory_consumable.item.unit',
            'inventory_consumable.item.category',
            'inventory_consumable.qrCode'
        ])
            ->whereHas('inventory_consumable')
            ->get()
            ->map(function ($d) {

                $d->item = $d->inventory_consumable->item ?? null;
                $d->qrCode = $d->inventory_consumable->qrCode ?? null;
                $d->item_type = 0;
                $d->dist_type = $d->type;

                return $d;
            });

        $nonConsumables = ItemDistribution::with([
            'inventory_non_consumable.item.unit',
            'inventory_non_consumable.item.category',
            'inventory_non_consumable.qrCode'
        ])
            ->whereHas('inventory_non_consumable')
            ->get()
            ->map(function ($d) {

                $d->item = $d->inventory_non_consumable->item ?? null;
                $d->qrCode = $d->inventory_non_consumable->qrCode ?? null;
                $d->item_type = 1;
                $d->dist_type = $d->type;

                return $d;
            });

        return $consumables
            ->concat($nonConsumables)
            ->sortByDesc('created_at')
            ->values();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $itemDistributions = $this->getItemDistributions();
        $categories = Category::all();

        $itemDistributions_table = view('item_distributions.table', compact('itemDistributions'))->render();
        return view('item_distributions.index', compact('itemDistributions_table', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $items = Item::with(['unit', 'inventoryConsumables', 'inventoryNonConsumables'])->get();

        // Only pre-select if item_id is passed via request
        $selectedItem = null;
        if ($request->has('item_id')) {
            $selectedItem = $items->find($request->item_id);
        }

        return view('item_distributions.form', compact('items', 'selectedItem'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:0,1',
            'inventory_ids' => 'required|array|min:1',
            'inventory_ids.*' => 'string',
            'status' => 'required|string|max:255', // use the form value
            'description' => 'nullable|string',
            'remarks' => 'nullable|string',
            'distribution_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request) {

            foreach ($request->inventory_ids as $inventoryId) {

                // Check if it's a consumable
                $consumable = InventoryConsumable::find($inventoryId);
                if ($consumable) {
                    ItemDistribution::create([
                        'type' => $request->type,
                        'description' => $request->description,
                        'remarks' => $request->remarks,
                        'quantity' => 1,
                        'distribution_date' => $request->distribution_date ?? now(),
                        'due_date' => $request->due_date,
                        'status' => $request->status,                 // <-- from form
                        'inventory_consumable_id' => $consumable->id,
                        'created_by' => auth()->id(),                // <-- auth helper
                        'updated_by' => auth()->id(),
                    ]);
                    continue; // skip to next inventory ID
                }

                // Otherwise, non-consumable
                $nonConsumable = InventoryNonConsumable::find($inventoryId);
                if ($nonConsumable) {
                    ItemDistribution::create([
                        'type' => $request->type,
                        'description' => $request->description,
                        'remarks' => $request->remarks,
                        'quantity' => 1,
                        'distribution_date' => $request->distribution_date ?? now(),
                        'due_date' => $request->due_date,
                        'status' => $request->status,                // <-- from form
                        'inventory_non_consumable_id' => $nonConsumable->id,
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);
                }
            }
        });

        // Get all distributions with inventory -> item
        $itemDistributions = $this->getItemDistributions();

        return response()->json([
            'html' => view('item_distributions.table', compact('itemDistributions'))->render(),
            'message' => 'New Distirbution added successfully',
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
    public function edit(string $id)
    {
        $itemDistribution = ItemDistribution::with([
            'inventory_consumable',
            'inventory_non_consumable'
        ])->findOrFail($id);

        $items = Item::with(['unit', 'inventoryConsumables', 'inventoryNonConsumables'])->get();

        // Set the selected item based on the distribution
        $selectedItem = $itemDistribution->inventory_consumable
            ? $itemDistribution->inventory_consumable->item
            : ($itemDistribution->inventory_non_consumable
                ? $itemDistribution->inventory_non_consumable->item
                : null);

        return view('item_distributions.form', compact('items', 'selectedItem', 'itemDistribution'));
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'type' => 'required|in:0,1',
            'inventory_ids' => 'required|array|min:1',
            'inventory_ids.*' => 'string',
            'status' => 'required|string|max:255',
            'description' => 'nullable|string',
            'remarks' => 'nullable|string',
            'distribution_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request, $id) {

            // Delete previous distributions for this inventory IDs
            // Optional: you can skip this if you want to update in place
            ItemDistribution::whereIn('id', [$id])->delete();

            // Loop through the new inventory IDs to update/create distributions
            foreach ($request->inventory_ids as $inventoryId) {

                // Check if it's a consumable
                $consumable = InventoryConsumable::find($inventoryId);
                if ($consumable) {
                    ItemDistribution::updateOrCreate(
                        ['id' => $id], // Update the current distribution
                        [
                            'type' => $request->type,
                            'description' => $request->description,
                            'remarks' => $request->remarks,
                            'quantity' => 1,
                            'distribution_date' => $request->distribution_date ?? now(),
                            'due_date' => $request->due_date,
                            'status' => $request->status,
                            'inventory_consumable_id' => $consumable->id,
                            'created_by' => auth()->id(),
                            'updated_by' => auth()->id(),
                        ]
                    );
                    continue;
                }

                // Otherwise, non-consumable
                $nonConsumable = InventoryNonConsumable::find($inventoryId);
                if ($nonConsumable) {
                    ItemDistribution::updateOrCreate(
                        ['id' => $id],
                        [
                            'type' => $request->type,
                            'description' => $request->description,
                            'remarks' => $request->remarks,
                            'quantity' => 1,
                            'distribution_date' => $request->distribution_date ?? now(),
                            'due_date' => $request->due_date,
                            'status' => $request->status,
                            'inventory_non_consumable_id' => $nonConsumable->id,
                            'created_by' => auth()->id(),
                            'updated_by' => auth()->id(),
                        ]
                    );
                }
            }
        });

        // Return updated table with all distributions
        $itemDistributions = ItemDistribution::with([
            'inventory_consumable.item',
            'inventory_non_consumable.item',
        ])->latest()->get();

        return response()->json([
            'html' => view('item_distributions.table', compact('itemDistributions'))->render(),
            'message' => 'Distribution updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the distribution
        $itemDistribution = ItemDistribution::find($id);

        if (!$itemDistribution) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item Distribution not found.',
            ], 404);
        }

        // Delete the distribution
        $itemDistribution->delete();

        $itemDistributions = ItemDistribution::all();

        return response()->json([
            'html' => view('item_distributions.table', compact('itemDistributions'))->render(),
            'message' => 'Item Distribution deleted successfully.',
        ]);
    }
}
