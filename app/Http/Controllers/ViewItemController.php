<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Units;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\QR_Code;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;

class ViewItemController extends Controller
{

    /**
     * Live Search for Inventory
     */
    public function liveSearch(Request $request)
    {
        $searchTerm = $request->input('query', '');
        $statusFilter = $request->input('status', null);
        $itemId = $request->input('item_id');

        $inventories = $this->getInventories($itemId);

        if ($searchTerm != '') {

            $searchLower = strtolower($searchTerm);

            $inventories = $inventories->filter(function ($inventory) use ($searchTerm, $searchLower) {

                if (!$inventory->item) return false;

                $match = false;

                if (stripos($inventory->item->name, $searchTerm) !== false) {
                    $match = true;
                }

                if (!empty($inventory->received_date)) {

                    try {

                        $formattedReceived = Carbon::parse($inventory->received_date)->format('M d, Y');

                        if (stripos($formattedReceived, $searchTerm) !== false) {
                            $match = true;
                        }
                    } catch (\Exception $e) {
                    }
                }

                if (!empty($inventory->warranty_expires)) {

                    try {

                        $formattedWarranty = Carbon::parse($inventory->warranty_expires)->format('M d, Y');

                        if (stripos($formattedWarranty, $searchTerm) !== false) {
                            $match = true;
                        }
                    } catch (\Exception $e) {
                    }
                }

                if (in_array($searchLower, ['consumable', 'con']) && $inventory->item->type == 0) {
                    $match = true;
                }

                if (in_array($searchLower, ['non-consumable', 'non']) && $inventory->item->type == 1) {
                    $match = true;
                }

                if (isset($inventory->distribution_status)) {

                    if (in_array($searchLower, ['available', 'avail']) && strtolower($inventory->distribution_status) === 'available') {
                        $match = true;
                    }

                    if (strtolower($inventory->distribution_status) === $searchLower) {
                        $match = true;
                    }
                }

                if (
                    $inventory->item->unit &&
                    stripos($inventory->item->unit->name, $searchTerm) !== false
                ) {
                    $match = true;
                }

                if (
                    $inventory->item->category &&
                    stripos($inventory->item->category->name, $searchTerm) !== false
                ) {
                    $match = true;
                }

                if (
                    $inventory->qrCode &&
                    stripos($inventory->qrCode->code, $searchTerm) !== false
                ) {
                    $match = true;
                }

                return $match;
            });
        }

        $statusFilterLower = strtolower($statusFilter ?? '');

        if (!in_array($statusFilterLower, ['all', 'all status']) && $statusFilterLower !== '') {

            $inventories = $inventories->filter(
                fn($inventory) =>
                isset($inventory->distribution_status) &&
                    strtolower($inventory->distribution_status) === $statusFilterLower
            );
        }

        $viewItems = $inventories->values();

        return view('inventory.viewItem.table', compact('viewItems'));
    }

    /**
     * Helper: Get inventories
     */
    private function getInventories($itemId)
    {

        return Inventory::with(['item', 'qrCode', 'itemDistributions'])
            ->where('item_id', $itemId)
            ->get()
            ->map(function ($inventory) {

                if ($inventory->itemDistributions->isEmpty()) {

                    $inventory->distribution_status = 'Available';
                } else {

                    $inventory->distribution_status =
                        $inventory->itemDistributions->last()->status ?? 'Available';
                }

                return $inventory;
            })
            ->sortByDesc('received_date')
            ->values();
    }

    /**
     * Create form
     */
    public function create($item = null)
    {

        $categories = Category::all();
        $units = Units::all();
        $items = Item::all();

        $selectedItem = $item
            ? Item::findOrFail($item)
            : $items->first();

        return view('inventory.viewItem.form', compact(
            'items',
            'selectedItem',
            'categories',
            'units'
        ));
    }

    /**
     * Store inventory
     */
    public function store(Request $request)
    {

        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'received_date' => 'required|date',
            'warranty_expires' => 'nullable|date',
        ]);

        $item = Item::findOrFail($request->item_id);

        $datetime = date('Ymd');
        $prefix = strtoupper(substr($item->name, 0, 1));

        $lastQrToday = QR_Code::where('code', 'like', "LCC-{$prefix}{$datetime}-%")
            ->orderByDesc('code')
            ->first();

        $lastSequence = 0;

        if ($lastQrToday) {

            $parts = explode('-', $lastQrToday->code);
            $lastSequence = (int) end($parts);
        }

        for ($i = 0; $i < $request->quantity; $i++) {

            $lastSequence++;

            $sequence = str_pad($lastSequence, 3, '0', STR_PAD_LEFT);

            $qrCodeValue = 'LCC-' . $prefix . $datetime . '-' . $sequence;

            $qrImageName = $qrCodeValue . '.svg';

            $qrImagePath = 'qrcodes/' . $qrImageName;

            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );

            $writer = new Writer($renderer);

            Storage::disk('public')->put($qrImagePath, $writer->writeString($qrCodeValue));

            $inventory = Inventory::create([
                'item_id' => $item->id,
                'received_date' => $request->received_date,
                'warranty_expires' => $item->type == 1 ? $request->warranty_expires : null,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            QR_Code::create([
                'code' => $qrCodeValue,
                'qr_picture' => $qrImagePath,
                'inventory_id' => $inventory->id,
                'status' => QR_Code::STATUS_USED,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        $item->increment('quantity', $request->quantity);

        $viewItems = $this->getInventories($item->id);

        return response()->json([
            'html' => view('inventory.viewItem.table', compact('viewItems'))->render(),
            'message' => 'Inventory added successfully',
        ]);
    }

    /**
     * Show inventory list
     */
    public function show($id)
    {

        $viewItem = Item::with(['category', 'unit'])->findOrFail($id);

        $viewItems = $this->getInventories($viewItem->id);

        $viewItems_table = view('inventory.viewItem.table', compact('viewItems', 'viewItem'))->render();

        return view('inventory.viewItem.index', compact(
            'viewItem',
            'viewItems',
            'viewItems_table'
        ));
    }

    /**
     * Edit inventory
     */
    public function edit($id)
    {

        $inventory = Inventory::with('item')->findOrFail($id);

        $items = Item::all();
        $categories = Category::all();
        $units = Units::all();

        $item = $inventory;
        $selectedItem = $inventory->item;

        return view('inventory.viewItem.form', compact(
            'items',
            'item',
            'selectedItem',
            'categories',
            'units'
        ));
    }

    /**
     * Update inventory
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'item_id' => 'required|exists:items,id',
            'received_date' => 'required|date',
            'warranty_expires' => 'nullable|date',
        ]);

        $inventory = Inventory::findOrFail($id);

        $inventory->update([
            'item_id' => $request->item_id,
            'received_date' => $request->received_date,
            'warranty_expires' => $request->warranty_expires,
            'updated_by' => Auth::id(),
        ]);

        $viewItems = $this->getInventories($inventory->item_id);

        return response()->json([
            'html' => view('inventory.viewItem.table', compact('viewItems'))->render(),
            'message' => 'Item updated successfully',
        ]);
    }

    /**
     * Delete inventory
     */
    public function destroy($id)
    {

        $inventory = Inventory::findOrFail($id);

        $itemId = $inventory->item_id;

        $inventory->delete();

        $item = Item::find($itemId);

        if ($item && $item->quantity > 0) {
            $item->decrement('quantity');
        }

        $viewItems = $this->getInventories($itemId);

        return response()->json([
            'html' => view('inventory.viewItem.table', compact('viewItems'))->render(),
            'message' => 'Inventory deleted successfully',
        ]);
    }
}
