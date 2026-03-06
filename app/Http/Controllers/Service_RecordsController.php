<?php

namespace App\Http\Controllers;

use App\Models\Service_Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $service_records = Service_Record::latest()->get();
        $serviceRecords_table = view('service_records.table', compact('service_records'))->render();
        return view('service_records.index', compact('serviceRecords_table', 'service_records'));
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
            'type' => 'required|integer|in:0,1',
            'schedule_date' => 'required|date',
            'encharge_person' => 'required|string',
            'description' => 'required|string',
            'picture' => 'nullable|image|max:2048', // optional image validation
        ]);

        $picturePath = null;

        // Handle picture upload once
        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $pictureName = time() . '_' . uniqid() . '.' . $picture->getClientOriginalExtension();
            $picture->storeAs('service_records', $pictureName, 'public');
            $picturePath = 'service_records/' . $pictureName;
        }

        $newRecords = [];

        foreach ($request->inventory_non_consumable_ids as $itemId) {
            $record = Service_Record::create([
                'inventory_non_consumable_id' => $itemId,
                'type' => $request->type,
                'schedule_date' => $request->schedule_date,
                'encharge_person' => $request->encharge_person,
                'description' => $request->description,
                'quantity' => 1,
                'picture' => $picturePath, // attach picture to each record
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $newRecords[] = $record;
        }

        $cards_html = '';
        foreach ($newRecords as $record) {
            $cards_html .= view('service_records.card', ['record' => $record])->render();
        }

        $service_records = Service_Record::latest()->get();

        $table_html = view('service_records.table', compact('service_records'))->render();

        return response()->json([
            'cards_html'  => $cards_html,
            'table_html' => $table_html,
            'message' => 'New Service Records added successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service_record = Service_Record::with('inventoryNonConsumable.item', 'inventoryNonConsumable.qrCode')
            ->findOrFail($id);

        $nonConsumables = InventoryNonConsumable::with(['item', 'qrCode'])->get();
        $units = Units::all();

        return view('service_records.view', compact('service_record', 'nonConsumables', 'units'));
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
            'type' => 'required|integer|in:0,1',
            'schedule_date' => 'required|date',
            'encharge_person' => 'required|string|max:255',
            'description' => 'nullable|string',
            'picture' => 'nullable|image|max:2048', // optional image validation
        ]);

        $service_record = Service_Record::findOrFail($id);

        // Handle picture upload if a new file is provided
        $picturePath = $service_record->picture; // default to existing picture
        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $pictureName = time() . '_' . uniqid() . '.' . $picture->getClientOriginalExtension();
            $picture->storeAs('service_records', $pictureName, 'public');
            $picturePath = 'service_records/' . $pictureName;
        }

        // Single item update only
        $service_record->update([
            'inventory_non_consumable_id' => $request->inventory_non_consumable_ids[0],
            'type' => $request->type,
            'schedule_date' => $request->schedule_date,
            'encharge_person' => $request->encharge_person,
            'description' => $request->description,
            'picture' => $picturePath,
            'updated_by' => Auth::id(),
        ]);

        $service_records = Service_Record::latest()->get();

        $cards_html = view('service_records.card', ['record' => $service_record])->render();
        $table_html = view('service_records.table', compact('service_records'))->render();

        return response()->json([
            'record_id'  => $service_record->id,
            'cards_html'  => $cards_html,
            'table_html' => $table_html,
            'message' => 'Service record updated successfully',
        ]);
    }


    /**
     * Mark a service as completed.
     */
    public function complete($id)
    {
        $service_record = Service_Record::findOrFail($id);

        $service_record->update([
            'completed_date' => now(),
            'updated_by' => Auth::id(),
        ]);

        // Reload all records for the table
        $service_records = Service_Record::latest()->get();

        // Render the updated card (for AJAX card replacement)
        $cards_html = view('service_records.card', ['record' => $service_record])->render();
        $table_html = view('service_records.table', compact('service_records'))->render();

        return response()->json([
            'record_id'  => $service_record->id,
            'card_html'  => $cards_html,
            'table_html' => $table_html,
            'message'    => 'Service marked as completed successfully.',
        ]);
    }

    /**
     * Undo a service completion.
     */
    public function undoCompletion($id)
    {
        $service_record = Service_Record::findOrFail($id);

        $message = 'Service is already incomplete.';

        if ($service_record->completed_date !== null) {
            $service_record->update([
                'completed_date' => null,
                'updated_by'     => Auth::id(),
            ]);

            $message = 'Service completion undone successfully.';
        }

        // Reload all records for the table
        $service_records = Service_Record::latest()->get();

        $cards_html = view('service_records.card', ['record' => $service_record])->render();
        $table_html = view('service_records.table', compact('service_records'))->render();

        return response()->json([
            'record_id'  => $service_record->id,
            'cards_html'  => $cards_html,
            'table_html' => $table_html,
            'message'    => $message,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the service record
        $service_record = Service_Record::findOrFail($id);

        // Delete it
        $service_record->delete();

        // Get updated list of service records
        $service_records = Service_Record::latest()->get();

        $table_html = view('service_records.table', compact('service_records'))->render();

        return response()->json([
            'record_id'  => $service_record->id,
            'table_html' => $table_html,
            'message' => 'Service record deleted successfully',
        ]);
    }
}
