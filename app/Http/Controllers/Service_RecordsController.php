<?php

namespace App\Http\Controllers;

use App\Models\Service_Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory;
use App\Models\Units;
use App\Models\Category;
use App\Models\Item;
use App\Models\InventoryHistory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

class Service_RecordsController extends Controller
{
    /**
     * Live Search for Item Distributions
     */
    public function liveSearch(Request $request)
    {
        $searchTerm = $request->input('query', '');
        $categoryFilter = $request->input('categories', null);
        $serviceTypeFilter = $request->input('service_type', null);

        // Base query: load service records with inventory/item relationships
        $query = Service_Record::with([
            'inventory.item.unit',
            'inventory.item.category',
            'inventory.qrCode'
        ]);

        // --- Search term filter ---
        if (!empty($searchTerm)) {
            $searchTermLower = strtolower($searchTerm);
            $query->where(function ($q) use ($searchTermLower) {
                $q->whereHas('inventory.item', function ($itemQuery) use ($searchTermLower) {
                    $itemQuery->whereRaw('LOWER(name) LIKE ?', ["%{$searchTermLower}%"])
                        ->orWhereHas('unit', fn($u) => $u->whereRaw('LOWER(name) LIKE ?', ["%{$searchTermLower}%"]))
                        ->orWhereHas('category', fn($c) => $c->whereRaw('LOWER(name) LIKE ?', ["%{$searchTermLower}%"]));
                })
                    ->orWhereHas('inventory.qrCode', fn($qrc) => $qrc->whereRaw('LOWER(code) LIKE ?', ["%{$searchTermLower}%"]))
                    ->orWhereRaw('LOWER(type) LIKE ?', ["%{$searchTermLower}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$searchTermLower}%"])
                    ->orWhereRaw('LOWER(technician) LIKE ?', ["%{$searchTermLower}%"])
                    ->orWhereRaw('LOWER(remarks) LIKE ?', ["%{$searchTermLower}%"])
                    ->orWhereRaw('DATE_FORMAT(service_date, "%b %d, %Y") LIKE ?', ["%{$searchTermLower}%"])
                    ->orWhereRaw('DATE_FORMAT(completed_date, "%b %d, %Y") LIKE ?', ["%{$searchTermLower}%"]);
            });
        }

        // --- Category Filter ---
        if (!empty($categoryFilter) && strtolower($categoryFilter) !== 'all') {
            $query->whereHas('inventory.item.category', fn($q) => $q->where('id', $categoryFilter));
        }

        // --- Service type filter ---
        if (!empty($serviceTypeFilter) && !str_contains(strtolower($serviceTypeFilter), 'all')) {
            $typeMap = [
                'maintenance' => 'maintenance',
                'installation' => 'installation',
                'inspection' => 'inspection',
            ];
            $lowerType = strtolower($serviceTypeFilter);
            if (isset($typeMap[$lowerType])) {
                $query->where('type', $typeMap[$lowerType]);
            }
        }

        $service_records = $query->latest()->get();

        return view('service_records.table', compact('service_records'));
    }

    /**
     * Helper: Get all Item Inventories
     */
    private function getInventories($perPage = 20)
    {
        $allItems = Item::with(['category', 'unit', 'inventories.qrCode'])->get()->map(function ($item) {
            $item->inventory_type = $item->type === 'consumable' ? 'Consumable' : 'Non-Consumable';
            $item->item_name = $item->name;

            $qrCodes = $item->inventories->pluck('qrCode')->filter();
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

    public function index()
    {
        $categories = Category::all();
        $service_records = Service_Record::latest()->get();
        $serviceRecords_table = view('service_records.table', compact('service_records'))->render();
        return view('service_records.index', compact('serviceRecords_table', 'service_records', 'categories'));
    }

    public function create(Request $request)
    {
        // 1️⃣ Get all items with inventories and unit
        $items = Item::with(['unit', 'inventories.qrCode'])->get();

        // 2️⃣ Get categories
        $categories = Category::all();

        // 3️⃣ Fetch the selected item if item_id is provided
        $selectedItem = null;
        $availableInventories = collect();
        $selectedInventory = null; // <- THIS WILL HOLD the clicked inventory

        if ($request->has('item_id')) {
            $selectedItem = Item::with(['unit', 'inventories.qrCode'])->find($request->item_id);

            if ($selectedItem) {
                // Available inventories (you can filter by status if needed)
                $availableInventories = $selectedItem->inventories;

                // If inventory_id is passed from JS, use it as selectedInventory
                if ($request->has('inventory_id')) {
                    $selectedInventory = $selectedItem->inventories
                        ->where('id', $request->inventory_id)
                        ->first()?->id; // null-safe
                }
            }
        }

        // 4️⃣ Quick flag
        $quickAction = $request->has('quick') && $request->quick == 1;

        // 5️⃣ Return view (or JSON for modal)
        return view('service_records.form', compact(
            'items',
            'selectedItem',
            'categories',
            'quickAction',
            'availableInventories',
            'selectedInventory'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventory_ids' => 'nullable|array',
            'inventory_ids.*' => 'nullable|uuid|exists:inventories,id',
            'type' => 'required|in:maintenance,installation,inspection',
            'service_date' => 'required|date',
            'completed_date' => 'nullable|date',
            'technician' => 'required|string|max:255',
            'description' => 'nullable|string',
            'remarks' => 'nullable|string',
            'picture' => 'nullable|image|max:2048',
            'item_id' => 'required|exists:items,id',
            'page' => 'nullable|string',
        ]);

        $picturePath = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('service_records', $filename, 'public');
            $picturePath = 'service_records/' . $filename;
        }

        $newRecords = [];
        $item = null; // make sure we have the item reference
        $inventoryIds = $request->inventory_ids ?? [];
        $serviceCount = count($inventoryIds);

        foreach ($inventoryIds as $inventoryId) {
            $inventory = Inventory::findOrFail($inventoryId);
            $item = $inventory->item;

            $record = Service_Record::create([
                'inventory_id' => $inventoryId,
                'type' => $request->type,
                'status' => 'scheduled',
                'service_date' => $request->service_date,
                'completed_date' => $request->completed_date,
                'technician' => $request->technician,
                'description' => $request->description,
                'remarks' => $request->remarks,
                'picture' => $picturePath,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $inventory->update(['status' => $request->type]);

            $inventory->update([
                'status' => $request->type,
                'holder' => $request->technician,
                'date_assigned' => now(),
                'notes' => $request->notes,
            ]);
            if ($item) {
                $item->decrement('remaining', 1);
            }

            $newRecords[] = $record;
        }

        // Log history
        if ($item) {
            InventoryHistory::create([
                'item_id' => $item->id,
                'inventory_id' => $inventoryId,
                'holder_or_borrower' => $request->technician,
                'action' => $request->type,
                'quantity' => $serviceCount,
                'notes' => $request->remarks ?? $request->description, // use description or remarks
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        if ($item) {
            $item->refresh();
        }

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
                'message'                  => 'Service added successfully'
            ]);
        } elseif ($request->page === 'inventory') {
            $inventories = $this->getInventories();
            return response()->json([
                'table_html' => view('inventory.inventory.table', compact('inventories'))->render(),
                'message'    => 'Inventory updated successfully',
            ]);
        } elseif ($request->page === 'service_records') {
            // Render service record cards
            $cards_html = '';
            foreach ($newRecords as $record) {
                $cards_html .= view('service_records.card', ['record' => $record])->render();
            }

            $service_records = Service_Record::latest()->get();
            $table_html = view('service_records.table', compact('service_records'))->render();
            return response()->json([
                'cards_html' => $cards_html,
                'table_html' => $table_html,
                'message'    => 'Service record(s) added successfully',
                'record_ids' => collect($newRecords)->pluck('id')->toArray(),
            ]);
        }
    }

    public function show(string $id)
    {
        $service_record = Service_Record::with('inventory.item', 'inventory.qrCode')->findOrFail($id);
        return view('service_records.view', compact('service_record'));
    }

    public function edit(string $id)
    {
        $service_record = Service_Record::with('inventory.item', 'inventory.qrCode')
            ->findOrFail($id);

        $items = Item::with(['unit', 'inventories.qrCode'])->get();
        $categories = Category::all();
        $units = Units::all();

        // Get the item from the service_record (for display)
        $selectedItem = optional($service_record->inventory)->item;

        return view('service_records.form', compact(
            'service_record',
            'items',
            'categories',
            'units',
            'selectedItem'
        ));
    }

    public function update(Request $request, string $id)
    {
        $record = Service_Record::findOrFail($id);

        // Conditional validation
        $rules = [
            'service_id' => 'required|exists:service_records,id',
            'technician' => 'required|string|max:255',
            'type'       => 'required|in:maintenance,installation,inspection',
            'service_date' => 'required|date',
            'completed_date' => 'nullable|date',
            'description' => 'nullable|string',
            'remarks' => 'nullable|string',
            'picture' => 'nullable|image|max:2048',
        ];

        // Only require inventory_ids if present in the form
        if ($request->has('inventory_ids')) {
            $rules['inventory_ids'] = 'required|array|min:1';
        }

        $request->validate($rules);

        // Handle picture upload
        $picturePath = $record->picture;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('service_records', $filename, 'public');
            $picturePath = 'service_records/' . $filename;
        }

        // Update record
        $record->update([
            'inventory_id'   => $request->inventory_ids[0] ?? $record->inventory_id, // keep existing if not provided
            'type'           => $request->type,
            'status'         => 'scheduled',
            'service_date'   => $request->service_date,
            'completed_date' => $request->completed_date,
            'technician'     => $request->technician,
            'description'    => $request->description,
            'remarks'        => $request->remarks,
            'picture'        => $picturePath,
            'updated_by'     => Auth::id(),
        ]);

        $service_records = Service_Record::latest()->get();

        return response()->json([
            'record_id'  => $record->id,
            'cards_html' => view('service_records.card', ['record' => $record])->render(),
            'table_html' => view('service_records.table', compact('service_records'))->render(),
            'message'    => 'Service record updated successfully',
        ]);
    }

    public function destroy(string $id)
    {
        $record = Service_Record::findOrFail($id);
        Inventory::where('id', $record->inventory_id)->update(['status' => null]);
        $record->delete();

        $service_records = Service_Record::latest()->get();

        return response()->json([
            'record_id'  => $record->id,
            'table_html' => view('service_records.table', compact('service_records'))->render(),
            'message'    => 'Service record deleted successfully',
        ]);
    }

    /**
     * Complete Service
     */
    public function show_service($id)
    {
        $service_record = Service_Record::with('inventory.item', 'inventory.qrCode')
            ->findOrFail($id);

        return view('service_records.complete_service_form', compact('service_record'));
    }

    public function complete_service(Request $request, $id)
    {
        // Validate request
        $request->validate([
            'completed_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'picture' => 'nullable|image|max:2048',
            'page' => 'nullable|string',
        ]);

        // Find the service record
        $record = Service_Record::with('inventory.item')->findOrFail($id);

        $inventory = $record->inventory;
        $item = $inventory->item ?? null;

        // Handle picture upload
        $picturePath = $record->picture;

        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('service_records', $filename, 'public');
            $picturePath = 'service_records/' . $filename;
        }

        // Update service record
        $record->update([
            'status' => 'completed',
            'completed_date' => $request->completed_date ?? now(),
            'remarks' => $request->remarks,
            'picture' => $picturePath,
            'updated_by' => Auth::id(),
        ]);

        // Reset inventory status
        if ($inventory) {
            $inventory->update([
                'status' => 'available',
                'holder' => null,
                'notes' => $request->remarks ?? 'Service completed',
                'date_assigned' => null,
                $item->increment('remaining', 1),
            ]);
        }

        // Log inventory history
        if ($item) {
            InventoryHistory::create([
                'item_id' => $item->id,
                'inventory_id' => $inventory->id,
                'holder_or_borrower' => null,
                'action' => 'service completed',
                'quantity' => 1,
                'notes' => $request->remarks ?? 'Service completed',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

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
        } else  if ($request->page === 'service') {
            // Refresh service records table
            $service_records = Service_Record::latest()->get();

            return response()->json([
                'record_id'  => $record->id,
                'cards_html' => view('service_records.card', ['record' => $record])->render(),
                'table_html' => view('service_records.table', compact('service_records'))->render(),
                'message'    => 'Service marked as completed successfully',
            ]);
        }
    }

    public function undoCompletion(string $id)
    {
        $record = Service_Record::findOrFail($id);
        $record->update([
            'status'         => 'under repair',
            'completed_date' => null,
            'updated_by'     => Auth::id(),
        ]);

        Inventory::where('id', $record->inventory_id)->update(['status' => $record->type]);

        $service_records = Service_Record::latest()->get();

        return response()->json([
            'record_id'  => $record->id,
            'cards_html' => view('service_records.card', ['record' => $record])->render(),
            'table_html' => view('service_records.table', compact('service_records'))->render(),
            'message'    => 'Service completion undone successfully',
        ]);
    }
}
