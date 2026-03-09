<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\ItemDistribution;
use App\Models\QR_Code;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemDistributionsController extends Controller
{
    /**
     * Live Search for Item Distributions
     */
    public function liveSearch(Request $request)
    {
        $searchTerm     = $request->input('query', '');
        $statusFilter   = $request->input('status', null);
        $categoryFilter = $request->input('category', null);
        $distTypeFilter = $request->input('dist_type', null);

        $distributions = $this->getItemDistributions();

        if ($searchTerm != '') {
            $searchLower = strtolower($searchTerm);

            $distributions = $distributions->filter(function ($dist) use ($searchTerm, $searchLower) {
                if (!$dist->inventory || !$dist->inventory->item) return false;

                $item = $dist->inventory->item;
                $match = false;

                // Search item name
                if (stripos($item->name, $searchTerm) !== false) $match = true;

                // Search unit
                if ($item->unit && stripos($item->unit->name, $searchTerm) !== false) $match = true;

                // Search category
                if ($item->category && stripos($item->category->name, $searchTerm) !== false) $match = true;

                // Search QR Code
                if ($dist->inventory->qrCode && stripos($dist->inventory->qrCode->code, $searchTerm) !== false) $match = true;

                // Search status, description, remarks
                foreach (['status', 'description', 'remarks'] as $field) {
                    if (!empty($dist->$field) && stripos($dist->$field, $searchTerm) !== false) $match = true;
                }

                // Distribution type keywords
                if (in_array($searchLower, ['distribution']) && $dist->type == 0) $match = true;
                if (in_array($searchLower, ['borrow', 'borrowed']) && $dist->type == 1) $match = true;

                // Distribution date & due date
                foreach (['distribution_date', 'due_date'] as $dateField) {
                    if (!empty($dist->$dateField) && $dist->$dateField != '--') {
                        try {
                            $formatted = Carbon::parse($dist->$dateField)->format('M d, Y');
                            if (stripos($formatted, $searchTerm) !== false) $match = true;
                        } catch (\Exception $e) {
                        }
                    }
                }

                return $match;
            });
        }

        // Status Filter
        if (!empty($statusFilter) && !str_contains(strtolower($statusFilter), 'all')) {
            $statusLower = strtolower($statusFilter);
            $distributions = $distributions->filter(fn($dist) => strtolower($dist->status) === $statusLower);
        }

        // Category Filter
        if (!empty($categoryFilter) && strtolower($categoryFilter) !== 'all') {
            $distributions = $distributions->filter(fn($dist) => $dist->inventory->item->category->id == $categoryFilter ?? false);
        }

        // Distribution Type Filter
        if (!empty($distTypeFilter) && !str_contains(strtolower($distTypeFilter), 'all')) {
            $distributions = $distributions->filter(
                fn($dist) =>
                strtolower($distTypeFilter) === 'distribution' ? $dist->type == 0
                    : (strtolower($distTypeFilter) === 'borrow' ? $dist->type == 1 : true)
            );
        }

        $itemDistributions = $distributions->values();
        return view('item_distributions.table', compact('itemDistributions'));
    }

    /**
     * Helper: Get all Item Distributions
     */
    private function getItemDistributions()
    {
        return ItemDistribution::with(['inventory.item.unit', 'inventory.item.category', 'inventory.qrCode'])
            ->get()
            ->sortByDesc('created_at')
            ->values();
    }

    /**
     * Index view
     */
    public function index()
    {
        $itemDistributions = $this->getItemDistributions();
        $categories = Category::all();
        $itemDistributions_table = view('item_distributions.table', compact('itemDistributions'))->render();

        return view('item_distributions.index', compact('itemDistributions_table', 'categories', 'itemDistributions'));
    }

    /**
     * Create form
     */

    public function create(Request $request)
    {
        $items = Item::with(['unit', 'inventories'])->get();
        $categories = Category::all(); // fetch all categories
        $selectedItem = $request->has('item_id') ? $items->find($request->item_id) : null;

        return view('item_distributions.form', compact('items', 'selectedItem', 'categories'));
    }

    /**
     * Store new distribution
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:0,1',
            'inventory_ids' => 'required|array|min:1',
            'inventory_ids.*' => 'string',
            'status' => 'required|string|max:255',
            'description' => 'nullable|string',
            'distribution_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request) {
            $transactionId = Str::uuid();

            foreach ($request->inventory_ids as $inventoryId) {
                $inventory = Inventory::findOrFail($inventoryId);

                ItemDistribution::create([
                    'type' => $request->type,
                    'description' => $request->description,
                    'quantity' => 1,
                    'distribution_date' => $request->distribution_date ?? now(),
                    'due_date' => $request->due_date,
                    'status' => $request->status,
                    'inventory_id' => $inventory->id,
                    'transaction_id' => $transactionId,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }
        });

        $itemDistributions = $this->getItemDistributions();

        return response()->json([
            'html' => view('item_distributions.table', compact('itemDistributions'))->render(),
            'message' => 'New Distribution added successfully',
        ]);
    }

    /**
     * Show distribution
     */
    public function show(string $id)
    {
        $itemDistribution = ItemDistribution::with(['inventory.item.unit', 'inventory.item.category'])->findOrFail($id);
        $items = Item::with(['unit', 'inventories'])->get();
        $selectedItem = $itemDistribution->inventory->item ?? null;

        return view('item_distributions.view', compact('items', 'selectedItem', 'itemDistribution'));
    }

    /**
     * Edit distribution
     */
    public function edit(string $id)
    {
        $itemDistribution = ItemDistribution::with(['inventory.item.unit', 'inventory.item.category'])->findOrFail($id);
        $items = Item::with(['unit', 'inventories'])->get();
        $categories = Category::all();
        $selectedItem = $itemDistribution->inventory->item ?? null;

        return view('item_distributions.form', compact('items', 'selectedItem', 'itemDistribution', 'categories'));
    }

    /**
     * Update distribution
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'type' => 'required|in:0,1',
            'inventory_ids' => 'required|array|min:1',
            'inventory_ids.*' => 'string',
            'status' => 'required|string|max:255',
            'description' => 'nullable|string',
            'distribution_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request, $id) {
            $existingDistribution = ItemDistribution::findOrFail($id);
            $inventoryId = $request->inventory_ids[0];
            $inventory = Inventory::findOrFail($inventoryId);

            $existingDistribution->update([
                'type' => $request->type,
                'description' => $request->description,
                'quantity' => 1,
                'distribution_date' => $request->distribution_date ?? now(),
                'due_date' => $request->due_date,
                'status' => $request->status,
                'inventory_id' => $inventory->id,
                'updated_by' => auth()->id(),
            ]);
        });

        $itemDistributions = $this->getItemDistributions();

        return response()->json([
            'html' => view('item_distributions.table', compact('itemDistributions'))->render(),
            'message' => 'Distribution updated successfully',
        ]);
    }

    /**
     * Mark as returned
     */
    public function return($id)
    {
        $itemDistribution = ItemDistribution::findOrFail($id);
        $itemDistribution->update([
            'status' => 'returned',
            'updated_by' => auth()->id(),
        ]);

        $itemDistributions = $this->getItemDistributions();

        return response()->json([
            'html' => view('item_distributions.table', compact('itemDistributions'))->render(),
            'message' => 'Return recorded successfully.',
        ]);
    }

    /**
     * Delete distribution
     */
    public function destroy(string $id)
    {
        $itemDistribution = ItemDistribution::findOrFail($id);
        $itemDistribution->delete();

        $itemDistributions = $this->getItemDistributions();

        return response()->json([
            'html' => view('item_distributions.table', compact('itemDistributions'))->render(),
            'message' => 'Item Distribution deleted successfully.',
        ]);
    }
}
