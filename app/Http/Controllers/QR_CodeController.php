<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QR_Code;
use App\Models\Units;
use App\Models\Category;
use App\Models\Item;
use Carbon\Carbon;

// use SimpleSoftwareIO\QrCode\Facades\QrCode as QrGenerator;

class QR_CodeController extends Controller
{
    /**
     * Live Search for Items.
     */
    public function liveSearch(Request $request)
    {
        $searchTerm = $request->input('query', '');
        $typeFilter = $request->input('type', null);
        $statusFilter = $request->input('status', null);
        $categoryFilter = $request->input('category', null);

        // Get all QR codes with related items
        $qrCodes = QR_Code::with([
            'inventoryConsumable.item.unit',
            'inventoryConsumable.item.category',
            'inventoryNonConsumable.item.unit',
            'inventoryNonConsumable.item.category',
        ])->latest()->get();

        // Filter by search term
        if ($searchTerm != '') {
            $searchLower = strtolower($searchTerm);

            $qrCodes = $qrCodes->filter(function ($qr) use ($searchTerm, $searchLower) {
                $item = $qr->inventoryConsumable->item ?? $qr->inventoryNonConsumable->item ?? null;
                if (!$item) return false;

                $match = false;

                // Search in item name or description
                if (stripos($item->name, $searchTerm) !== false || stripos($item->description ?? '', $searchTerm) !== false) {
                    $match = true;
                }

                // Search in category
                if ($item->category && stripos($item->category->name, $searchTerm) !== false) {
                    $match = true;
                }

                // Search in unit
                if ($item->unit && stripos($item->unit->name, $searchTerm) !== false) {
                    $match = true;
                }

                // Search in QR code value
                if (stripos($qr->code, $searchTerm) !== false) {
                    $match = true;
                }

                // Search in QR code status (change available → active)
                $statusMap = [
                    QR_Code::STATUS_ACTIVE => 'active',
                    QR_Code::STATUS_USED => 'used',
                    QR_Code::STATUS_EXPIRED => 'expired',
                ];
                if (isset($statusMap[$qr->status]) && stripos($statusMap[$qr->status], $searchTerm) !== false) {
                    $match = true;
                }

                // Search in created_at (date string)
                if (!empty($qr->created_at) && $qr->created_at != '--') {
                    try {
                        $formattedWarranty = Carbon::parse($qr->created_at)->format('M d, Y');
                        if (stripos($formattedWarranty, $searchTerm) !== false) {
                            $match = true;
                        }
                    } catch (\Exception $e) {
                        // Ignore invalid dates
                    }
                }

                // Search in created_by (assuming it's a relation)
                if ($qr->user && stripos($qr->user->name ?? '', $searchTerm) !== false) {
                    $match = true;
                }

                return $match;
            });
        }

        // Filter by type
        if ($typeFilter && strtolower($typeFilter) != 'all') {
            $qrCodes = $qrCodes->filter(function ($qr) use ($typeFilter) {
                $item = $qr->inventoryConsumable->item ?? $qr->inventoryNonConsumable->item ?? null;
                if (!$item) return false;

                if (strtolower($typeFilter) === 'consumable') return $item->type == 0;
                if (in_array(strtolower($typeFilter), ['non-consumable', 'non'])) return $item->type == 1;

                return true;
            });
        }

        // Filter by status (QR code only)
        if (!empty($statusFilter) && strtolower($statusFilter) != 'all status') {
            $qrCodes = $qrCodes->filter(function ($qr) use ($statusFilter) {
                $status = strtolower($statusFilter);

                if ($status === 'used') return $qr->status === QR_Code::STATUS_USED;
                if ($status === 'active') return $qr->status === QR_Code::STATUS_ACTIVE;
                if ($status === 'expired') return $qr->status === QR_Code::STATUS_EXPIRED;

                return true;
            });
        }

        // Filter by category
        if ($categoryFilter && strtolower($categoryFilter) != 'all') {
            $qrCodes = $qrCodes->filter(function ($qr) use ($categoryFilter) {
                $item = $qr->inventoryConsumable->item ?? $qr->inventoryNonConsumable->item ?? null;
                return $item && $item->category && $item->category->id == $categoryFilter;
            });
        }

        // Reset keys
        $qrCodes = $qrCodes->values();

        return view('reference.qr_code.table', compact('qrCodes'));
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
     * Display list of QR codes
     */
    public function index()
    {
        $categories = Category::all();

        $qrCodes = QR_Code::with([
            'inventoryConsumable.item',
            'inventoryNonConsumable.item'
        ])->latest()->paginate(10);

        $qrCodes_table = view('reference.qr_code.table', compact('qrCodes'))->render();
        return view('reference.qr_code.index', compact('qrCodes_table', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Generate new QR Code
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show QR Image
     */
    public function show($id)
    {
        $categories = Category::all();
        $units = Units::all();

        $item = Item::findOrFail($id);

        return view('inventory.items.form', compact('item', 'categories', 'units'));
    }

    /**
     * Mark as Used
     */
    public function markUsed($id)
    {
        //
    }

    /**
     * Delete QR Code
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Print QR Label
     */
    public function printLabel($id)
    {
        $qr = QR_Code::findOrFail($id);

        return view('reference.qr_code.print_label', compact('qr'));
    }
}
