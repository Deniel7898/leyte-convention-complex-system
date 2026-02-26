<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Units;
use App\Models\InventoryConsumable;
use App\Models\InventoryNonConsumable;
use App\Models\QR_Code;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd; // already imported

class ItemsController extends Controller
{

    /**
     * Dynamic Item Status.
     */
    // private function updateItemStatus($itemId)
    // {
    //     $remaining = InventoryConsumable::where('item_id', $itemId)->count()
    //         + InventoryNonConsumable::where('item_id', $itemId)->count();

    //     $item = Item::find($itemId);

    //     if ($item) {
    //         $item->status = $remaining > 0 ? 1 : 0;
    //         $item->save();
    //     }
    // }

    /**
     * Live Search for Items.
     */
    public function liveSearch(Request $request)
    {
        $searchTerm = $request->input('query', '');
        $typeFilter = $request->input('type', null);
        $statusFilter = $request->input('status', null);
        $categoryFilter = $request->input('category', null);

        // Use getItems() helper to fetch all items with remaining, unit, category
        $items = $this->getItems();

        // Apply search filters on the collection
        if ($searchTerm != '') {
            $searchLower = strtolower($searchTerm);

            $items = $items->filter(function ($item) use ($searchTerm, $searchLower) {
                $match = false;

                // Text search in name or description
                if (stripos($item->name, $searchTerm) !== false || stripos($item->description, $searchTerm) !== false) {
                    $match = true;
                }

                // Numeric search in quantity
                if (is_numeric($searchTerm) && $item->quantity == $searchTerm) {
                    $match = true;
                }

                // Search in unit name
                if ($item->unit && stripos($item->unit->name, $searchTerm) !== false) {
                    $match = true;
                }

                // Search in category name
                if ($item->category && stripos($item->category->name, $searchTerm) !== false) {
                    $match = true;
                }

                // Type mapping keywords
                if (in_array($searchLower, ['consumable', 'con']) && $item->type == 0) {
                    $match = true;
                }
                if (in_array($searchLower, ['non-consumable', 'non', 'non consumable']) && $item->type == 1) {
                    $match = true;
                }

                // status mapping keywords
                if (in_array($searchLower, ['available', 'avail']) && $item->remaining > 0) {
                    $match = true;
                }

                if (in_array($searchLower, ['not-available', 'not available', 'not']) && $item->remaining == 0) {
                    $match = true;
                }

                return $match;
            });
        }

        // Apply type filter (dropdown)
        if ($typeFilter && strtolower($typeFilter) != 'all') {
            $items = $items->filter(function ($item) use ($typeFilter) {
                if (strtolower($typeFilter) === 'consumable') return $item->type == 0;
                if (in_array(strtolower($typeFilter), ['non-consumable', 'non'])) return $item->type == 1;
                return true;
            });
        }

        // Apply status filter (dropdown)
        if (!empty($statusFilter) && strtolower($statusFilter) !== 'all status') {

            $wantAvailable = strtolower($statusFilter) === 'available';

            $items = $items->filter(
                fn($item) =>
                $wantAvailable ? $item->remaining > 0 : $item->remaining == 0
            );
        }

        // Apply category filter (dropdown)
        if ($categoryFilter && strtolower($categoryFilter) != 'all') {
            $items = $items->filter(function ($item) use ($categoryFilter) {
                return $item->category && $item->category->id == $categoryFilter;
            });
        }

        // Reset keys after filtering
        $items = $items->values();

        return view('inventory.items.table', compact('items'));
    }

    /**
     * Helper: get items with remaining, unit, category
     */
    private function getItems()
    {
        return Item::with([
            'unit',
            'category',
            'inventoryConsumables.itemDistributions',
            'inventoryNonConsumables.itemDistributions'
        ])
            ->get()
            ->map(function ($item) {

                // Count only available inventory
                if ($item->type == 0) {
                    $remaining = $item->inventoryConsumables
                        ->filter(function ($inv) {
                            return $inv->itemDistributions
                                ->whereIn('status', ['distributed', 'borrowed', 'pending'])
                                ->isEmpty();
                        })
                        ->count();
                } else {
                    $remaining = $item->inventoryNonConsumables
                        ->filter(function ($inv) {
                            return $inv->itemDistributions
                                ->whereIn('status', ['distributed', 'borrowed', 'pending'])
                                ->isEmpty();
                        })
                        ->count();
                }

                $isAvailable = $remaining > 0;

                return (object)[
                    'id' => $item->id,
                    'name' => $item->name,
                    'type' => $item->type,
                    'quantity' => $item->quantity,
                    'remaining' => $remaining,
                    'is_available' => $isAvailable, // 👈 add this
                    'unit' => $item->unit ?? null,
                    'category' => $item->category ?? null,
                    'description' => $item->description ?? '--',
                    'picture' => $item->picture ?? null,
                ];
            });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = $this->getItems();
        $categories = Category::all();
        $items_table = view('inventory.items.table', compact('items'))->render();
        return view('inventory.items.index', compact('items_table', 'items', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $units = Units::all();
        return view('inventory.items.form', compact('categories', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|integer|in:0,1',
            'category_id' => 'required|integer',
            'quantity' => 'required|integer',
            'unit_id' => 'required|integer',
            'description' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'received_date' => 'nullable|date',
            'warranty_expires' => 'nullable|date', // For non-consumable
        ]);

        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $pictureName = time() . '_' . uniqid() . '.' . $picture->getClientOriginalExtension();
            $picture->storeAs('items', $pictureName, 'public');
            $validated['picture'] = 'items/' . $pictureName;
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

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

        $items = $this->getItems(); // Recalculate remaining

        return response()->json([
            'html' => view('inventory.items.table', compact('items'))->render(),
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $categories = Category::all();
        $units = Units::all();

        $item = Item::findOrFail($id);

        // Fetch the first inventory record based on item type
        $inventory = $item->type == 0
            ? InventoryConsumable::where('item_id', $item->id)->first()
            : InventoryNonConsumable::where('item_id', $item->id)->first();

        return view('inventory.items.form', compact('item', 'categories', 'units', 'inventory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|integer|in:0,1', // 0 = consumable, 1 = non-consumable
            'category_id' => 'required|integer',
            'quantity' => 'required|integer',
            'unit_id' => 'required|integer',
            'description' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            // 'warranty_expires' => 'nullable|date', // validate date format
        ]);

        // Handle picture upload
        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $pictureName = time() . '_' . uniqid() . '.' . $picture->getClientOriginalExtension();
            $picture->storeAs('items', $pictureName, 'public');
            $validated['picture'] = 'items/' . $pictureName;
        }

        $validated['updated_by'] = Auth::id();

        // Update the item
        $item = Item::findOrFail($id);
        $item->update($validated);

        // Update warranty_expires for non-consumables only
        // if ($item->type == 1) { // 1 = non-consumable
        //     $nonConsumable = InventoryNonConsumable::firstOrCreate(
        //         ['item_id' => $item->id],
        //         ['warranty_expires' => $validated['warranty_expires'] ?? null]
        //     );

        //     // If record already exists, update it
        //     if (!$nonConsumable->wasRecentlyCreated) {
        //         $nonConsumable->update([
        //             'warranty_expires' => $validated['warranty_expires'] ?? null,
        //         ]);
        //     }
        // }

        $items = $this->getItems(); // Recalculate remaining

        return response()->json([
            'html' => view('inventory.items.table', compact('items'))->render(),
            'message' => 'Item updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Item::findOrFail($id);
        $item->delete();

        $items = $this->getItems(); // Recalculate remaining

        return response()->json([
            'html' => view('inventory.items.table', compact('items'))->render(),
        ]);
    }
}
