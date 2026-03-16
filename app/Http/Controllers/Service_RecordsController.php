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

class Service_RecordsController extends Controller
{
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
        $service_records = Service_Record::latest()->get();
        $serviceRecords_table = view('service_records.table', compact('service_records'))->render();
        return view('service_records.index', compact('serviceRecords_table', 'service_records'));
    }

    public function create(Request $request)
    {
        $items = Item::with(['unit', 'inventories.qrCode'])->get();
        $categories = Category::all();
        $selectedItem = $request->has('item_id')
            ? Item::with(['unit', 'inventories.qrCode'])->find($request->item_id)
            : null;

        return view('service_records.form', compact('items', 'selectedItem', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventory_ids' => 'required|array',
            'inventory_ids.*' => 'exists:inventories,id',
            'type' => 'required|in:maintenance,installation,inspection',
            'service_date' => 'required|date',
            'completed_date' => 'nullable|date',
            'technician' => 'required|string|max:255',
            'description' => 'nullable|string',
            'remarks' => 'nullable|string',
            'picture' => 'nullable|image|max:2048',
            'item_id' => 'nullable|exists:items,id',
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
        $serviceCount = count($request->inventory_ids); // move outside the loop

        foreach ($request->inventory_ids as $inventoryId) {
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

            if ($item) {
                $item->decrement('remaining', 1);
            }

            $newRecords[] = $record;
        }

        // Log history
        if ($item) {
            InventoryHistory::create([
                'item_id' => $item->id,
                'action' => $request->type,
                'quantity' => $serviceCount,
                'notes' => $request->remarks ?? $request->description, // use description or remarks
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        // Render service record cards
        $cards_html = '';
        foreach ($newRecords as $record) {
            $cards_html .= view('service_records.card', ['record' => $record])->render();
        }

        $service_records = Service_Record::latest()->get();
        $table_html = view('service_records.table', compact('service_records'))->render();

        // Handle different page responses
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
        } elseif ($request->page === 'inventory') {
            $inventories = $this->getInventories();
            return response()->json([
                'table_html' => view('inventory.inventory.table', compact('inventories'))->render(),
                'message'    => 'Inventory updated successfully',
            ]);
        } elseif ($request->page === 'service_records') {
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
        $service_record = Service_Record::with('inventory.item', 'inventory.qrCode')->findOrFail($id);
        $items = Item::with(['unit', 'inventories.qrCode'])->get();
        $categories = Category::all();
        $units = Units::all();
        return view('service_records.form', compact('service_record', 'items', 'categories', 'units'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'inventory_ids' => 'required|array',
            'type' => 'required|in:maintenance,installation,inspection',
            'status' => 'required|in:scheduled,in progress,completed',
            'service_date' => 'required|date',
            'completed_date' => 'nullable|date',
            'technician' => 'required|string|max:255',
            'description' => 'nullable|string',
            'remarks' => 'nullable|string',
            'picture' => 'nullable|image|max:2048',
        ]);

        $record = Service_Record::findOrFail($id);

        $picturePath = $record->picture;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('service_records', $filename, 'public');
            $picturePath = 'service_records/' . $filename;
        }

        $record->update([
            'inventory_id'   => $request->inventory_ids[0],
            'type'           => $request->type,
            'status'         => $request->status,
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
                'status' => null,
                $item->increment('remaining', 1),
            ]);
        }

        // Log inventory history
        if ($item) {
            InventoryHistory::create([
                'item_id' => $item->id,
                'action' => 'service completed',
                'quantity' => 1,
                'notes' => $request->remarks ?? 'Service completed',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        // Refresh service records table
        $service_records = Service_Record::latest()->get();

        return response()->json([
            'record_id'  => $record->id,
            'cards_html' => view('service_records.card', ['record' => $record])->render(),
            'table_html' => view('service_records.table', compact('service_records'))->render(),
            'message'    => 'Service marked as completed successfully',
        ]);
    }

    public function undoCompletion(string $id)
    {
        $record = Service_Record::findOrFail($id);
        $record->update([
            'status'         => 'in progress',
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
