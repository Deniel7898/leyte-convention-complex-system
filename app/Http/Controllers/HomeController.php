<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Service_Record;
use App\Models\Item;
use App\Models\User;
use App\Models\Inventory;
use App\Models\ItemDistribution;
use App\Models\InventoryHistory;
use App\Models\QR_Code;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $recent_activities = InventoryHistory::orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $today = now()->toDateString();

        return view('home', [
            // Count of service records that require attention
            'item_service_required' => Service_Record::whereIn('status', ['scheduled', 'under repair', 'cancelled'])->count(),

            // Total stock in items table
            'total_stock' => Item::sum('total_stock'),

            // Total remaining in items table
            'total_remaining' => Item::sum('remaining'),

            // Total remaining 
            'total_category' => Category::count(),

            // Total users 
            'total_users' => User::count(),

            // Total Items Added Today 
            'items_added_today' => Inventory::whereDate('created_at', $today)->count(),

            // Total Distribution Today 
            'items_distributed' => ItemDistribution::whereIn('status', ['distributed', 'issued', 'borrowed'])->count(),

            // Total Service Today 
            'services_logged' => Service_Record::whereDate('created_at', $today)->count(),

            // Recent Activities
            'recent_activities' => $recent_activities,
        ]);
    }

    public function getItemByQrCode($code)
    {
        try {
            $qr = \App\Models\QR_Code::with([
                'inventory.item.category',
                'inventory.item.unit',
                'inventory.item.inventories' => function ($query) {
                    // Only include units that are not borrowed or issued
                    $query->whereNotIn('status', ['borrowed', 'issued']);
                }
            ])->where('code', $code)->first();

            if (!$qr) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code not found'
                ], 404);
            }

            $inventory = $qr->inventory;
            $item = $inventory->item ?? null;

            $availableUnits = [];
            $remainingQty = 0;

            if ($item) {
                // For consumables, just return remaining quantity
                if ($item->type === 'consumable') {
                    $remainingQty = $item->remaining ?? 0;
                } else {
                    // For non-consumables, calculate available units
                    foreach ($item->inventories as $inv) {
                        $hasActiveService = $inv->serviceRecords()
                            ->whereIn('status', ['scheduled', 'in progress'])
                            ->exists();

                        $hasDistributed = $inv->itemDistributions()
                            ->whereIn('type', ['distributed'])
                            ->exists();

                        if (!$hasActiveService && !$hasDistributed && !in_array(strtolower($inv->status), ['borrowed', 'issued'])) {
                            $availableUnits[] = [
                                'id' => $inv->id,
                                'qr_code' => $inv->qrCode->code ?? 'N/A',
                            ];
                        }
                    }

                    $remainingQty = count($availableUnits);
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'item_id'   => $item->id ?? null,
                    'item_name' => $item->name ?? 'N/A',
                    'category'  => $item->category->name ?? 'N/A',
                    'type'      => $item->type ?? 'N/A',   // consumable or non-consumable
                    'unit'      => $item->unit->name ?? 'N/A',
                    'supplier'  => $item->supplier ?? 'N/A',
                    'remaining' => $remainingQty,
                    'units'     => $availableUnits,        // only for non-consumables
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
