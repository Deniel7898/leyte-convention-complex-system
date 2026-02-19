<?php

namespace App\Http\Controllers;

use App\Models\Purchase_Request;
use App\Models\ItemsPurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Purchase_RequestsController extends Controller
{
    // ✅ PRINT ALL APPROVED
    public function printApproved()
    {
        $approvedRequests = Purchase_Request::with(['creator', 'items'])
            ->where('status', 'approved')
            ->get();

        return view('purchase_request.print_all', compact('approvedRequests'));
    }

    public function index(Request $request)
    {
        $query = Purchase_Request::with('creator');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('request_date', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('creator', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->orderBy('id', 'asc')
                          ->paginate(10);

        return view('purchase_request.index', compact('requests'));
    }

    public function create()
    {
        return view('purchase_request.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'request_date' => 'required|date',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        $pr = Purchase_Request::create([
            'request_date' => $request->request_date,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        foreach ($request->items as $item) {
            ItemsPurchaseRequest::create([
                'purchase_request_id' => $pr->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->route('purchase_request.index')
            ->with('success', 'Purchase Request created successfully.');
    }

    public function show(Purchase_Request $purchaseRequest)
    {
        $purchaseRequest->load('items', 'creator');

        return view('purchase_request.show', compact('purchaseRequest'));
    }

    public function updateStatus($id, $status)
    {
        $pr = Purchase_Request::findOrFail($id);

        $pr->update([
            'status' => $status,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Status updated successfully.');
    }

    public function destroy(Purchase_Request $purchaseRequest)
    {
        if (!in_array($purchaseRequest->status, ['pending', 'rejected'])) {
            return response()->json([
                'error' => 'Approved requests cannot be deleted.'
            ], 403);
        }

        $purchaseRequest->delete();

        $requests = Purchase_Request::with('creator')
                        ->latest()
                        ->paginate(10);

        $html = view('purchase_request.table', compact('requests'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }
}
