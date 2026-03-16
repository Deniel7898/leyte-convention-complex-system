<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\ItemDistribution;
use App\Models\InventoryHistory;
use App\Models\QR_Code;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

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
     * Helper: Get all Item Inventories
     */
    private function getInventories($perPage = 20)
    {
        $allItems = Item::with(['category', 'unit', 'inventories.qrCode'])->get()->map(function ($item) {

            $item->inventory_type = $item->type === 'consumable' ? 'Consumable' : 'Non-Consumable';
            $item->item_name = $item->name;

            // Gather all QR codes linked to inventories
            $qrCodes = $item->inventories->pluck('qrCode')->filter(); // removes nulls
            $item->qrCodes = $qrCodes;
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
     * Helper: Get all Item Distributions
     */
    private function getItemDistributions()
    {
        return ItemDistribution::with([
            'inventory.item.unit',     
            'inventory.item.category',
            'inventory.qrCode',        
        ])
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
        // Get all items with their inventories and unit
        $items = Item::with(['unit', 'inventories.qrCode'])->get();

        // Get all categories
        $categories = Category::all();

        // Fetch the selected item properly if item_id is provided
        $selectedItem = null;
        if ($request->has('item_id')) {
            $selectedItem = Item::with(['unit', 'inventories.qrCode'])
                ->find($request->item_id);
        }

        // Return the modal form view
        return view('item_distributions.form', compact('items', 'selectedItem', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:distributed,borrowed,issued',
            'status' => 'required|in:completed,borrowed,returned',
            'item_id' => 'required|exists:items,id',
            'inventory_ids' => 'nullable|array',
            'inventory_ids.*' => 'nullable|uuid|exists:inventories,id',
            'quantity' => 'nullable|integer|min:1',
            'distribution_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'department_or_borrower' => 'required|string|max:255',
            'page' => 'nullable|string',
        ]);

        $item = Item::findOrFail($request->item_id);

        DB::transaction(function () use ($request, $item) {
            $department = $request->department_or_borrower ?? 'Unknown';

            if ($item->type === 'consumable') {
                $quantity = $request->quantity ?? 1;

                if ($quantity > $item->remaining) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'quantity' => ['The requested quantity exceeds available stock.'],
                    ]);
                }

                ItemDistribution::create([
                    'transaction_id' => Str::uuid(),
                    'type' => $request->type,
                    'quantity' => $quantity,
                    'distribution_date' => $request->distribution_date ?? now(),
                    'due_date' => $request->due_date,
                    'status' => $request->status,
                    'item_id' => $item->id,
                    'inventory_id' => null,
                    'department_or_borrower' => $department,
                    'notes' => $request->notes,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                $item->decrement('remaining', $quantity);
                $item->decrement('total_stock', $quantity);
                $distributedCount = $quantity;
            } else {
                // Non-consumable
                $availableInventories = $item->inventories->filter(function ($inv) {
                    $excludedStatuses = ['borrowed'];
                    $serviceExcluded = ['maintenance', 'repair', 'installation'];
                    return !in_array(strtolower($inv->status ?? ''), $excludedStatuses)
                        && !in_array(strtolower($inv->service_status ?? ''), $serviceExcluded);
                });

                $inventoryIds = $request->inventory_ids ?? $availableInventories->pluck('id')->toArray();

                if (empty($inventoryIds)) {
                    throw new \Exception("No available inventories to distribute.");
                }

                foreach ($inventoryIds as $invId) {
                    $inventory = Inventory::findOrFail($invId);

                    ItemDistribution::create([
                        'transaction_id' => Str::uuid(),
                        'type' => $request->type,
                        'quantity' => 1,
                        'distribution_date' => $request->distribution_date ?? now(),
                        'due_date' => $request->due_date,
                        'status' => $request->status,
                        'item_id' => $item->id,
                        'inventory_id' => $inventory->id,
                        'department_or_borrower' => $department,
                        'notes' => $request->notes,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);

                    $inventory->update([
                        'status' => $request->type,
                        'holder' => $department,
                        'date_assigned' => now(),
                        'due_date' => $request->due_date,
                        'notes' => $request->notes,
                    ]);
                }

                $item->decrement('remaining', count($inventoryIds));
                $distributedCount = count($inventoryIds);
            }

            // Log history
            InventoryHistory::create([
                'item_id' => $item->id,
                'action' => $request->type,
                'quantity' => $distributedCount,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        });

        $item->refresh();

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

            $inventories = $this->getInventories();
            return response()->json([
                'table_html' => view('inventory.inventory.table', compact('inventories'))->render(),
                'message'    => 'Stock added successfully'
            ]);
        }
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
            'status' => 'required|in:completed,borrowed,returned',
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
                'updated_by' => Auth::id(),
            ]);
        });

        $itemDistributions = $this->getItemDistributions();

        return response()->json([
            'distribution_html' => view('item_distributions.table', compact('itemDistributions'))->render(),
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
            'updated_by' => Auth::id(),
        ]);

        $itemDistributions = $this->getItemDistributions();

        return response()->json([
            'distribution_html' => view('item_distributions.table', compact('itemDistributions'))->render(),
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
            'distribution_html' => view('item_distributions.table', compact('itemDistributions'))->render(),
            'message' => 'Item Distribution deleted successfully.',
        ]);
    }

    public function showReturnForm($id)
    {
        $distribution = ItemDistribution::with('inventory.item', 'inventory.qrCode')->findOrFail($id);
        return view('item_distributions.return_form', compact('distribution'));
    }

    public function returnItem(Request $request, $id)
    {
        // Validate request
        $request->validate([
            'returned_date' => 'required|date',
            'notes' => 'nullable|string',
            'type' => 'nullable',
            'status' => 'nullable',
            'item_id' => 'nullable',
            'department_or_borrower' => 'nullable|string',
        ]);

        // Find the item distribution
        $distribution = ItemDistribution::with('inventory.item')->findOrFail($id);

        // Update the record
        $distribution->update([
            'returned_date' => $request->returned_date,
            'notes' => $request->notes,
            'status' => 'returned',
            'item_id' => $request->item_id ?? $distribution->item_id,
            'department_or_borrower' => $request->department_or_borrower ?? $distribution->department_or_borrower,
            'updated_by' => Auth::id(),
        ]);

        // Optional: update inventory status if needed
        if ($distribution->inventory) {
            $distribution->inventory->update([
                'status' => null
            ]);
        }

        // Optional: log history
        if ($distribution->inventory && $distribution->inventory->item) {
            InventoryHistory::create([
                'item_id' => $distribution->inventory->item->id,
                'action' => 'returned',
                'quantity' => 1,
                'notes' => $request->notes ?? 'Returned item',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        // Refresh the item distributions table
        $itemDistributions = ItemDistribution::latest()->get();

        return response()->json([
            'distribution_id' => $distribution->id,
            'table_html' => view('item_distributions.table', compact('itemDistributions'))->render(),
            'message' => 'Item returned successfully!',
        ]);
    }
}
