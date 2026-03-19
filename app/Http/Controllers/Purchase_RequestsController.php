<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase_Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class Purchase_RequestsController extends Controller
{
    /**
     * Helper: Get paginated purchase requests table
     */
    private function getPurchaseRequestsTable($purchaseRequests = null, $perPage = 10)
    {
        if ($purchaseRequests === null) {
            $purchaseRequests = Purchase_Request::with(['creator'])
                ->paginate($perPage);
        }

        return view('purchase_requests.table', compact('purchaseRequests'))->render();
    }

    /**
     * Display listing
     */
    public function index()
    {
        $purchaseRequests = Purchase_Request::with(['creator'])
            ->paginate(10);

        return view('purchase_requests.index', [
            'purchaseRequests' => $purchaseRequests,
            'purchase_requests_table' => $this->getPurchaseRequestsTable($purchaseRequests),
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('purchase_requests.form');
    }

    /**
     * Store new purchase request (JSON items)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.description' => 'nullable|string',
        ]);

        Purchase_Request::create([
            'request_date' => $validated['request_date'],
            'status' => 'pending',
            'items' => $validated['items'],
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return $this->getPurchaseRequestsTable();
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        $purchaseRequest = Purchase_Request::findOrFail($id);
        return view('purchase_requests.form', compact('purchaseRequest'));
    }

    /**
     * Update purchase request
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'request_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.description' => 'nullable|string',
        ]);

        $purchaseRequest = Purchase_Request::findOrFail($id);

        // Prevent updates to non-pending requests
        if ($purchaseRequest->status !== 'pending') {
            abort(403, 'Cannot update a purchase request that is not pending.');
        }

        $purchaseRequest->update([
            'request_date' => $validated['request_date'],
            'items' => $validated['items'],
            'updated_by' => Auth::id(),
        ]);

        return $this->getPurchaseRequestsTable();
    }

    /**
     * Delete purchase request
     */
    public function destroy($id)
    {
        $purchaseRequest = Purchase_Request::findOrFail($id);

        // Prevent deletion of non-pending requests
        if ($purchaseRequest->status !== 'pending') {
            abort(403, 'Cannot delete a purchase request that is not pending.');
        }

        $purchaseRequest->delete();

        return $this->getPurchaseRequestsTable();
    }

    /**
     * Print single purchase request
     */
    public function print($id)
    {
        $purchaseRequest = Purchase_Request::with(['creator'])
            ->findOrFail($id);

        return view('purchase_requests.print', compact('purchaseRequest'));
    }

    /**
     * Search items (for autocomplete suggestions)
     */
    public function searchItems(Request $request)
    {
        $search = $request->input('search', '');

        $items = Item::where('name', 'like', '%' . $search . '%')
            ->limit(10)
            ->get(['id', 'name'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                ];
            });

        return response()->json($items);
    }
}