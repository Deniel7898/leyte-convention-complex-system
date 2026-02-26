<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Units;
use App\Models\Item;
use App\Models\InventoryConsumable;
use App\Models\InventoryNonConsumable;
use App\Models\QR_Code;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd; // already imported

class InventoriesController extends Controller
{
    /**
     * Live Search for Inventory
     */
    public function liveSearch(Request $request)
    {
        $searchTerm = $request->input('query', '');
        $typeFilter = $request->input('type', null);
        $statusFilter = $request->input('status', null);
        $categoryFilter = $request->input('category', null);

        $inventories = $this->getInventories();

        // Apply text search
        if ($searchTerm != '') {
            $searchLower = strtolower($searchTerm);

            $inventories = $inventories->filter(function ($inventory) use ($searchTerm, $searchLower) {

                if (!$inventory->item) return false;

                $match = false;

                // Search item name
                if (stripos($inventory->item->name, $searchTerm) !== false) {
                    $match = true;
                }

                // Search received date
                if (!empty($inventory->received_date) && $inventory->received_date != '--') {
                    try {
                        $formattedReceived = Carbon::parse($inventory->received_date)->format('M d, Y');
                        if (stripos($formattedReceived, $searchTerm) !== false) {
                            $match = true;
                        }
                    } catch (\Exception $e) {
                        // Ignore invalid dates
                    }
                }

                // Search warranty date
                if (!empty($inventory->warranty_expires) && $inventory->warranty_expires != '--') {
                    try {
                        $formattedWarranty = Carbon::parse($inventory->warranty_expires)->format('M d, Y');
                        if (stripos($formattedWarranty, $searchTerm) !== false) {
                            $match = true;
                        }
                    } catch (\Exception $e) {
                        // Ignore invalid dates
                    }
                }

                // Type keywords
                if (in_array($searchLower, ['consumable', 'con']) && $inventory->item->type == 0) {
                    $match = true;
                }
                if (in_array($searchLower, ['non-consumable', 'non', 'non consumable']) && $inventory->item->type == 1) {
                    $match = true;
                }

                // Status keywords
                if (in_array($searchLower, ['available', 'avail']) && strtolower($inventory->distribution_status) === 'available') {
                    $match = true;
                }

                if (
                    in_array($searchLower, ['distributed', 'borrowed', 'pending', 'partial', 'returned', 'received']) &&
                    strtolower($inventory->distribution_status) === $searchLower
                ) {
                    $match = true;
                }

                // Search in unit name
                if (
                    $inventory->item->unit &&
                    stripos($inventory->item->unit->name, $searchTerm) !== false
                ) {
                    $match = true;
                }

                // Search in category name
                if (
                    $inventory->item->category &&
                    stripos($inventory->item->category->name, $searchTerm) !== false
                ) {
                    $match = true;
                }

                // Search in QR code
                if (!empty($inventory->qrCode->code) && stripos($inventory->qrCode->code, $searchTerm) !== false) {
                    $match = true;
                }

                return $match;
            });
        }

        // Apply type filter
        if (!empty($typeFilter) && strtolower($typeFilter) !== 'all type') {
            $inventories = $inventories->filter(function ($inventory) use ($typeFilter) {

                if (!$inventory->item) return false;

                if (strtolower($typeFilter) === 'consumable') {
                    return $inventory->item->type == 0;
                }

                if (strtolower($typeFilter) === 'non-consumable') {
                    return $inventory->item->type == 1;
                }

                return true;
            });
        }

        // Apply status filter
        if (!empty($statusFilter) && strtolower($statusFilter) !== 'all status') {

            $statusFilterLower = strtolower($statusFilter);

            $inventories = $inventories->filter(function ($inventory) use ($statusFilterLower) {
                if (!$inventory->distribution_status) return false;

                return strtolower($inventory->distribution_status) === $statusFilterLower;
            });
        }

        // Apply category filter
        if (!empty($categoryFilter) && strtolower($categoryFilter) !== 'all') {
            $inventories = $inventories->filter(function ($inventory) use ($categoryFilter) {

                if (!$inventory->item || !$inventory->item->category) {
                    return false;
                }

                return $inventory->item->category->id == $categoryFilter;
            });
        }

        $inventories = $inventories->values();

        return view('inventory.inventory.table', compact('inventories'));
    }

    /**
     * Helper: Get all inventories (Consumable + Non-Consumable)
     */
    private function getInventories()
    {
        $consumables = InventoryConsumable::with(['item', 'qrCode', 'itemDistributions'])
            ->get()
            ->map(function ($c) {
                $c->inventory_type = 'Consumable';
                $c->warranty_expires = '--';
                $c->item_name = $c->item->name ?? '--';

                // Determine status from distributions
                if ($c->itemDistributions->isEmpty()) {
                    $c->distribution_status = 'Available';
                } else {
                    // Pick the most recent distribution status
                    $c->distribution_status = $c->itemDistributions->last()->status ?? 'Available';
                }

                return $c;
            });

        $nonConsumables = InventoryNonConsumable::with(['item', 'qrCode', 'itemDistributions'])
            ->get()
            ->map(function ($n) {
                $n->inventory_type = 'Non-Consumable';
                $n->warranty_expires = $n->warranty_expires ?? '--';
                $n->item_name = $n->item->name ?? '--';

                // Determine status from distributions
                if ($n->itemDistributions->isEmpty()) {
                    $n->distribution_status = 'Available';
                } else {
                    $n->distribution_status = $n->itemDistributions->last()->status ?? 'Available';
                }

                return $n;
            });

        return $consumables->concat($nonConsumables)
            ->sortByDesc('received_date')
            ->values();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all inventories (consumables + non-consumables)
        $inventories = $this->getInventories();

        $categories = Category::all();

        $inventories_table = view('inventory.inventory.table', compact('inventories'))->render();

        return view('inventory.inventory.index', compact('inventories_table', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $units = Units::all();
        return view('inventory.inventory.form', compact('categories', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|integer|in:0,1',
            'category_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'unit_id' => 'required|integer',
            'description' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'received_date' => 'nullable|date',
            'warranty_expires' => 'nullable|date', // For non-consumable
        ]);

        // Handle picture upload
        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $pictureName = time() . '_' . uniqid() . '.' . $picture->getClientOriginalExtension();
            $picture->storeAs('items', $pictureName, 'public');
            $validated['picture'] = 'items/' . $pictureName;
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        // Save the new item and get it in a variable
        $item = Item::create($validated);

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

        // Loop once per quantity
        for ($i = 0; $i < $item->quantity; $i++) {
            $lastSequence++;
            $sequence = str_pad($lastSequence, 3, '0', STR_PAD_LEFT);
            $qrCodeValue = 'LCC-' . $prefix . $datetime . '-' . $sequence;
            $qrImageName = $qrCodeValue . '.svg'; // .svg now
            $qrImagePath = 'qrcodes/' . $qrImageName;

            // Use Svg backend for QR generation
            $renderer = new ImageRenderer(
                new RendererStyle(200), // QR size
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);

            Storage::disk('public')->put($qrImagePath, $writer->writeString($qrCodeValue));

            if ($item->type == 0) {
                $consumable = InventoryConsumable::create([
                    'id' => Str::uuid(),
                    'item_id' => $item->id,
                    'received_date' => $request->received_date,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                QR_Code::create([
                    'code' => $qrCodeValue,
                    'qr_picture' => $qrImagePath,
                    'inventory_consumable_id' => $consumable->id,
                    'status' => QR_Code::STATUS_USED,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            } else {
                $nonConsumable = InventoryNonConsumable::create([
                    'id' => Str::uuid(),
                    'item_id' => $item->id,
                    'received_date' => $request->received_date,
                    'warranty_expires' => $request->warranty_expires,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                QR_Code::create([
                    'code' => $qrCodeValue,
                    'qr_picture' => $qrImagePath,
                    'inventory_non_consumable_id' => $nonConsumable->id,
                    'status' => QR_Code::STATUS_USED,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
        }

        // Get all inventories (consumables + non-consumables)
        $inventories = $this->getInventories();

        // Return the table partial with inventories
        return response()->json([
            'html' => view('inventory.inventory.table', compact('inventories'))->render(),
            'message' => 'Item added successfully',
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
     * Show the form for editing the specified inventory.
     */
    public function edit($id)
    {
        $categories = Category::all();
        $units = Units::all();

        // Try to find the inventory in consumable or non-consumable tables
        $inventory = InventoryConsumable::with('item')->find($id);
        $inventoryType = 'consumable';

        if (!$inventory) {
            $inventory = InventoryNonConsumable::with('item')->findOrFail($id);
            $inventoryType = 'non-consumable';
        }

        return view('inventory.inventory.form', compact('inventory', 'categories', 'units', 'inventoryType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'received_date' => 'nullable|date',
            'warranty_expires' => 'nullable|date',
        ]);

        // Try consumable first
        try {
            $inventory = InventoryConsumable::with('item')->findOrFail($id);
            $isNonConsumable = false;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $inventory = InventoryNonConsumable::with('item')->findOrFail($id);
            $isNonConsumable = true;
        }

        // Always update received_date for both
        $updateData = [
            'received_date' => $validated['received_date'] ?? null,
            'updated_by' => Auth::id(),
        ];

        // Add warranty only if non-consumable
        if ($isNonConsumable) {
            $updateData['warranty_expires'] = $validated['warranty_expires'] ?? null;
        }

        $inventory->update($updateData);

        // Update item updated_by
        $inventory->item->update([
            'updated_by' => Auth::id(),
        ]);

        $inventories = $this->getInventories();

        return response()->json([
            'html' => view('inventory.inventory.table', compact('inventories'))->render(),
            'message' => 'Inventory updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Try to find in consumables first
        $inventoryConsumable = InventoryConsumable::find($id);
        $inventoryNonConsumable = InventoryNonConsumable::find($id);

        if ($inventoryConsumable) {
            $itemId = $inventoryConsumable->item_id;
            $inventoryConsumable->delete();
        } elseif ($inventoryNonConsumable) {
            $itemId = $inventoryNonConsumable->item_id;
            $inventoryNonConsumable->delete();
        } else {
            return response()->json(['error' => 'Inventory not found'], 404);
        }

        // Decrement item quantity safely
        $item = Item::find($itemId);
        if ($item && $item->quantity > 0) {
            $item->decrement('quantity');
        }

        // Get all inventories (consumables + non-consumables)
        $inventories = $this->getInventories();

        return response()->json([
            'html' => view('inventory.inventory.table', compact('inventories'))->render(),
            'message' => 'Inventory deleted successfully',
        ]);
    }
}
