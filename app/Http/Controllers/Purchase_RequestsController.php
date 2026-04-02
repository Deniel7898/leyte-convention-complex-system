<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase_Request;
use Illuminate\Support\Facades\Auth;

class Purchase_RequestsController extends Controller
{
    public function index()
    {
        $requests = Purchase_Request::latest()->paginate(10);
        return view('purchase_requests.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'request_date' => 'required|date',
            'items' => 'required|array'
        ]);

        Purchase_Request::create([
            'request_date' => $request->request_date,
            'items' => $request->items, // ✅ ARRAY (Laravel converts to JSON)
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return response()->json(
            Purchase_Request::findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'request_date' => 'required|date',
            'items' => 'required|array'
        ]);

        $purchase = Purchase_Request::findOrFail($id);

        $purchase->update([
            'request_date' => $request->request_date,
            'items' => $request->items,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true]);
    }

    public function print($id)
    {
        $request = Purchase_Request::findOrFail($id);

        return view('purchase_requests.print', compact('request'));
    }

    public function destroy($id)
    {
        Purchase_Request::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}