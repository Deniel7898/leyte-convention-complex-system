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
        $searchTerm     = $request->input('query', '');
        $typeFilter     = $request->input('type', null);
        $statusFilter   = $request->input('status', null);
        $categoryFilter = $request->input('category', null);

        $qrCodes = $this->getQRCodes();

        if ($searchTerm != '') {
            $searchLower = strtolower($searchTerm);

            $qrCodes = $qrCodes->filter(function ($qr) use ($searchTerm, $searchLower) {
                $item = $qr->item;
                if (!$item) return false;

                $match = false;

                if (stripos($item->name, $searchTerm) !== false || stripos($item->description ?? '', $searchTerm) !== false) {
                    $match = true;
                }

                // Type keywords (Consumable)
                if (in_array($searchLower, ['consumable', 'con']) && $item->type == 0) {
                    $match = true;
                }

                // Type keywords (Non-Consumable)
                if (in_array($searchLower, ['non-consumable', 'non', 'non consumable']) && $item->type == 1) {
                    $match = true;
                }
                // Search Unit
                if ($item->unit && stripos($item->unit->name, $searchTerm) !== false) $match = true;

                // Search Category
                if ($item->category && stripos($item->category->name, $searchTerm) !== false) $match = true;

                // Search QR Code
                if (stripos($qr->code, $searchTerm) !== false) $match = true;

                // Status keywords
                $statusMap = [
                    QR_Code::STATUS_ACTIVE  => 'active',
                    QR_Code::STATUS_USED    => 'used',
                    QR_Code::STATUS_EXPIRED => 'expired',
                ];
                if (isset($statusMap[$qr->status]) && stripos($statusMap[$qr->status], $searchTerm) !== false) {
                    $match = true;
                }

                // Search Genrated date
                if (!empty($item->created_at) && $item->created_at != '--') {
                    try {
                        $formattedReceived = Carbon::parse($item->created_at)->format('M d, Y');
                        if (stripos($formattedReceived, $searchTerm) !== false) {
                            $match = true;
                        }
                    } catch (\Exception $e) {
                        // Ignore invalid dates
                    }
                }

                // Created by
                if ($qr->user && stripos($qr->user->name ?? '', $searchTerm) !== false) {
                    $match = true;
                }

                return $match;
            });
        }

        // Filter by type
        if ($typeFilter && strtolower($typeFilter) != 'all') {
            $qrCodes = $qrCodes->filter(function ($qr) use ($typeFilter) {
                $item = $qr->item;
                if (!$item) return false;

                if (strtolower($typeFilter) === 'consumable') return $item->type == 0;
                if (in_array(strtolower($typeFilter), ['non-consumable', 'non'])) return $item->type == 1;

                return true;
            });
        }

        // Filter by status
        if (!empty($statusFilter) && strtolower($statusFilter) != 'all status') {
            $statusLower = strtolower($statusFilter);

            $qrCodes = $qrCodes->filter(function ($qr) use ($statusLower) {
                $statusMap = [
                    'active'  => QR_Code::STATUS_ACTIVE,
                    'used'    => QR_Code::STATUS_USED,
                    'expired' => QR_Code::STATUS_EXPIRED,
                ];
                return isset($statusMap[$statusLower]) && $qr->status === $statusMap[$statusLower];
            });
        }

        // Filter by category
        if ($categoryFilter && strtolower($categoryFilter) != 'all') {
            $qrCodes = $qrCodes->filter(function ($qr) use ($categoryFilter) {
                return $qr->item && $qr->item->category && $qr->item->category->id == $categoryFilter;
            });
        }

        $qrCodes = $qrCodes->values();

        return view('reference.qr_code.table', compact('qrCodes'));
    }

    /**
     * Helper: Get all QR codes with related items (Consumable + Non-Consumable)
     */
    private function getQRCodes()
    {
        $consumableQRCodes = QR_Code::with(['inventoryConsumable.item.unit', 'inventoryConsumable.item.category', 'user'])
            ->whereHas('inventoryConsumable')
            ->get()
            ->map(function ($qr) {
                $qr->item = $qr->inventoryConsumable->item ?? null;
                $qr->item_type = 0; // Consumable
                return $qr;
            });

        $nonConsumableQRCodes = QR_Code::with(['inventoryNonConsumable.item.unit', 'inventoryNonConsumable.item.category', 'user'])
            ->whereHas('inventoryNonConsumable')
            ->get()
            ->map(function ($qr) {
                $qr->item = $qr->inventoryNonConsumable->item ?? null;
                $qr->item_type = 1; // Non-Consumable
                return $qr;
            });

        return $consumableQRCodes
            ->concat($nonConsumableQRCodes)
            ->sortByDesc('created_at')
            ->values();
    }

    /**
     * Display list of QR codes
     */
    public function index()
    {
        $categories = Category::all();

        // Use helper to get all QR codes
        $qrCodes = $this->getQRCodes();

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
