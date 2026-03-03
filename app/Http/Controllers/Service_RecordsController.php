<?php

namespace App\Http\Controllers;

use App\Models\Service_Record;
use Illuminate\Http\Request;
use App\Models\InventoryNonConsumable;
use App\Models\Category;
use App\Models\Units;

class Service_RecordsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service_records = Service_Record::all();
        $serviceRecords_table = view('service_records.table', compact('service_records'))->render();
        return view('service_records.index', compact('serviceRecords_table'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nonConsumables = InventoryNonConsumable::with('item')->get();
        $units = Units::all();

        return view('service_records.form', compact('nonConsumables', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'inventory_non_consumable_ids' => 'required|array',
        ]);

        foreach ($request->inventory_non_consumable_ids as $itemId) {
            Service_Record::create([
                'inventory_non_consumable_id' => $itemId,
                'schedule_date' => $request->schedule_date,
                'encharge_person' => $request->encharge_person,
                'description' => $request->description,
                'quantity' => 1,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

        $service_records = Service_Record::all();

        return response()->json([
            'html' => view('service_records.table', compact('service_records'))->render(),
            'message' => 'New Service Records added successfully',
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
        $service_record = Service_Record::with('inventoryNonConsumable.item', 'inventoryNonConsumable.qrCode')
            ->findOrFail($id);

        $nonConsumables = InventoryNonConsumable::with(['item', 'qrCode'])->get();
        $units = Units::all();

        return view('service_records.form', compact('service_record', 'nonConsumables', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'inventory_non_consumable_ids' => 'required|array',
            'schedule_date' => 'required|date',
            'encharge_person' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $service_record = Service_Record::findOrFail($id);

        // If you allow **multiple non-consumables per record**, you may need to delete old ones and create new
        // Otherwise, update the single record
        if (count($request->inventory_non_consumable_ids) > 1) {
            // Wrap in transaction
            \DB::transaction(function () use ($service_record, $request) {
                // Delete old records
                Service_Record::where('schedule_date', $service_record->schedule_date)
                    ->where('encharge_person', $service_record->encharge_person)
                    ->delete();

                // Create new records
                foreach ($request->inventory_non_consumable_ids as $itemId) {
                    Service_Record::create([
                        'inventory_non_consumable_id' => $itemId,
                        'schedule_date' => $request->schedule_date,
                        'encharge_person' => $request->encharge_person,
                        'description' => $request->description,
                        'quantity' => 1,
                        'created_by' => $service_record->created_by, // keep original creator
                        'updated_by' => auth()->id(),
                    ]);
                }
            });
        } else {
            // Single item update
            $service_record->update([
                'inventory_non_consumable_id' => $request->inventory_non_consumable_ids[0],
                'schedule_date' => $request->schedule_date,
                'encharge_person' => $request->encharge_person,
                'description' => $request->description,
                'updated_by' => auth()->id(),
            ]);
        }

        $service_records = Service_Record::all();
        $serviceRecords_table = view('service_records.table', compact('service_records'))->render();

        return response()->json([
            'html' => $serviceRecords_table,
            'message' => 'Service record updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the service record
        $serviceRecord = Service_Record::findOrFail($id);

        // Delete it
        $serviceRecord->delete();

        // Get updated list of service records
        $service_records = Service_Record::all();

        // Render the updated table
        $serviceRecords_table = view('service_records.table', compact('service_records'))->render();

        return response()->json([
            'html' => $serviceRecords_table,
            'message' => 'Service record deleted successfully',
        ]);
    }
}
