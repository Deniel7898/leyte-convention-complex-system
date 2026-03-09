<?php

namespace App\Http\Controllers;

use App\Models\Service_Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory;
use App\Models\Units;
use App\Models\Category;

class Service_RecordsController extends Controller
{
    public function index()
    {
        $service_records = Service_Record::latest()->get();
        $serviceRecords_table = view('service_records.table', compact('service_records'))->render();

        return view('service_records.index', compact('serviceRecords_table', 'service_records'));
    }

    public function create()
    {
        $inventory = Inventory::with('item')->get();
        $categories = Category::all();
        $units = Units::all();

        return view('service_records.form', compact('inventory', 'units', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventory_ids' => 'required|array',
            'type' => 'required|integer|in:0,1',
            'schedule_date' => 'required|date',
            'encharge_person' => 'required|string|max:255',
            'description' => 'nullable|string',
            'picture' => 'nullable|image|max:2048',
        ]);

        $picturePath = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('service_records', $filename, 'public');
            $picturePath = 'service_records/' . $filename;
        }

        $newRecords = [];
        foreach ($request->inventory_ids as $itemId) {
            $newRecords[] = Service_Record::create([
                'inventory_id' => $itemId,
                'type' => $request->type,
                'schedule_date' => $request->schedule_date,
                'encharge_person' => $request->encharge_person,
                'description' => $request->description,
                'quantity' => 1,
                'picture' => $picturePath,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        $cards_html = '';
        foreach ($newRecords as $record) {
            $cards_html .= view('service_records.card', ['record' => $record])->render();
        }

        $service_records = Service_Record::latest()->get();
        $table_html = view('service_records.table', compact('service_records'))->render();

        return response()->json([
            'cards_html' => $cards_html,
            'table_html' => $table_html,
            'message' => 'New Service Records added successfully',
        ]);
    }

    public function show(string $id)
    {
        $service_record = Service_Record::with('inventory.item', 'inventory.qrCode')->findOrFail($id);
        $inventory = Inventory::with(['item', 'qrCode'])->get();
        $units = Units::all();

        return view('service_records.view', compact('service_record', 'inventory', 'units'));
    }

    public function edit(string $id)
    {
        $service_record = Service_Record::with('inventory.item', 'inventory.qrCode')->findOrFail($id);
        $inventory = Inventory::with(['item', 'qrCode'])->get();
          $categories = Category::all();
        $units = Units::all();

        return view('service_records.form', compact('service_record', 'inventory', 'units', 'categories'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'inventory_ids' => 'required|array',
            'type' => 'required|integer|in:0,1',
            'schedule_date' => 'required|date',
            'encharge_person' => 'required|string|max:255',
            'description' => 'nullable|string',
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
            'inventory_id' => $request->inventory_ids[0],
            'type' => $request->type,
            'schedule_date' => $request->schedule_date,
            'encharge_person' => $request->encharge_person,
            'description' => $request->description,
            'picture' => $picturePath,
            'updated_by' => Auth::id(),
        ]);

        $service_records = Service_Record::latest()->get();

        return response()->json([
            'record_id' => $record->id,
            'cards_html' => view('service_records.card', ['record' => $record])->render(),
            'table_html' => view('service_records.table', compact('service_records'))->render(),
            'message' => 'Service record updated successfully',
        ]);
    }

    public function complete(string $id)
    {
        $record = Service_Record::findOrFail($id);
        $record->update([
            'completed_date' => now(),
            'updated_by' => Auth::id(),
        ]);

        $service_records = Service_Record::latest()->get();

        return response()->json([
            'record_id' => $record->id,
            'cards_html' => view('service_records.card', ['record' => $record])->render(),
            'table_html' => view('service_records.table', compact('service_records'))->render(),
            'message' => 'Service marked as completed successfully',
        ]);
    }

    public function undoCompletion(string $id)
    {
        $record = Service_Record::findOrFail($id);
        $message = 'Service is already incomplete';
        if ($record->completed_date) {
            $record->update([
                'completed_date' => null,
                'updated_by' => Auth::id(),
            ]);
            $message = 'Service completion undone successfully';
        }

        $service_records = Service_Record::latest()->get();

        return response()->json([
            'record_id' => $record->id,
            'cards_html' => view('service_records.card', ['record' => $record])->render(),
            'table_html' => view('service_records.table', compact('service_records'))->render(),
            'message' => $message,
        ]);
    }

    public function destroy(string $id)
    {
        $record = Service_Record::findOrFail($id);
        $record->delete();

        $service_records = Service_Record::latest()->get();

        return response()->json([
            'record_id' => $record->id,
            'table_html' => view('service_records.table', compact('service_records'))->render(),
            'message' => 'Service record deleted successfully',
        ]);
    }
}
