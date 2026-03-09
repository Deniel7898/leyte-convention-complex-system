<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Units;
use App\Models\Inventory;
use App\Models\QR_Code;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;

class ItemsController extends Controller
{

    /**
     * Live Search for Items.
     */
    public function liveSearch(Request $request)
    {
        $searchTerm = $request->input('query', '');
        $statusFilter = $request->input('status', null);
        $categoryFilter = $request->input('category', null);

        $items = $this->getItems();

        if ($searchTerm != '') {

            $searchLower = strtolower($searchTerm);

            $items = $items->filter(function ($item) use ($searchTerm, $searchLower) {

                $match = false;

                if (
                    stripos($item->name, $searchTerm) !== false ||
                    stripos($item->description, $searchTerm) !== false
                ) {
                    $match = true;
                }

                if (is_numeric($searchTerm) && $item->quantity == $searchTerm) {
                    $match = true;
                }

                if ($item->unit && stripos($item->unit->name, $searchTerm) !== false) {
                    $match = true;
                }

                if ($item->category && stripos($item->category->name, $searchTerm) !== false) {
                    $match = true;
                }

                if (in_array($searchLower, ['non-consumable', 'non']) && $item->type == 1) {
                    $match = true;
                }

                if (in_array($searchLower, ['available', 'avail']) && $item->remaining > 0) {
                    $match = true;
                }

                if (in_array($searchLower, ['not', 'not available']) && $item->remaining == 0) {
                    $match = true;
                }

                return $match;
            });
        }

        if (!empty($statusFilter) && strtolower($statusFilter) !== 'all status') {

            $wantAvailable = strtolower($statusFilter) === 'available';

            $items = $items->filter(
                fn($item) =>
                $wantAvailable ? $item->remaining > 0 : $item->remaining == 0
            );
        }

        if ($categoryFilter && strtolower($categoryFilter) != 'all') {

            $items = $items->filter(function ($item) use ($categoryFilter) {

                return $item->category && $item->category->id == $categoryFilter;
            });
        }

        $items = $items->values();

        return view('inventory.items.table', compact('items'));
    }

    /**
     * Helper: get items with remaining inventory
     */
    private function getItems()
    {
        return Item::with([
            'unit',
            'category',
            'inventories.itemDistributions'
        ])
            ->get()
            ->map(function ($item) {

                $remaining = $item->inventories
                    ->filter(function ($inv) {

                        return $inv->itemDistributions
                            ->whereIn('status', ['distributed', 'borrowed', 'pending'])
                            ->isEmpty();
                    })
                    ->count();

                return (object)[
                    'id' => $item->id,
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'remaining' => $remaining,
                    'is_available' => $remaining > 0,
                    'unit' => $item->unit ?? null,
                    'category' => $item->category ?? null,
                    'description' => $item->description ?? '--',
                    'picture' => $item->picture ?? null,
                ];
            });
    }

    /**
     * Display items
     */
    public function index()
    {
        $items = $this->getItems();
        $categories = Category::all();

        $items_table = view('inventory.items.table', compact('items'))->render();

        return view('inventory.items.index', compact('items_table', 'items', 'categories'));
    }

    /**
     * Create form
     */
    public function create()
    {
        $categories = Category::all();
        $units = Units::all();

        return view('inventory.items.form', compact('categories', 'units'));
    }

    /**
     * Store item
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'quantity' => 'required|integer',
            'unit_id' => 'required|integer',
            'description' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'received_date' => 'nullable|date',
            'warranty_expires' => 'nullable|date',
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

        for ($i = 0; $i < $item->quantity; $i++) {

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
                'id' => Str::uuid(),
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

        $items = $this->getItems();

        return response()->json([
            'html' => view('inventory.items.table', compact('items'))->render(),
            'message' => 'Item added successfully',
        ]);
    }

    /**
     * Edit form
     */
    public function edit(string $id)
    {

        $categories = Category::all();
        $units = Units::all();

        $item = Item::findOrFail($id);

        $inventory = Inventory::where('item_id', $item->id)->first();

        return view('inventory.items.form', compact('item', 'categories', 'units', 'inventory'));
    }

    /**
     * Update item
     */
    public function update(Request $request, string $id)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|integer|in:0,1',
            'category_id' => 'required|integer',
            'quantity' => 'required|integer',
            'unit_id' => 'required|integer',
            'description' => 'nullable|string',
            'picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('picture')) {

            $picture = $request->file('picture');

            $pictureName = time() . '_' . uniqid() . '.' . $picture->getClientOriginalExtension();

            $picture->storeAs('items', $pictureName, 'public');

            $validated['picture'] = 'items/' . $pictureName;
        }

        $validated['updated_by'] = Auth::id();

        $item = Item::findOrFail($id);

        $item->update($validated);

        $items = $this->getItems();

        return response()->json([
            'html' => view('inventory.items.table', compact('items'))->render(),
            'message' => 'Item updated successfully',
        ]);
    }

    /**
     * Delete item
     */
    public function destroy(string $id)
    {

        $item = Item::findOrFail($id);

        $item->delete();

        $items = $this->getItems();

        return response()->json([
            'html' => view('inventory.items.table', compact('items'))->render(),
        ]);
    }
}