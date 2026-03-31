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
        $recent_activities = InventoryHistory::orderBy('created_at', 'desc')->get();

        $today = now()->toDateString();

        // stats array
        $stats = [
            // Total stock in items table
            'total_stock' => Item::sum('total_stock'),

            // Total remaining in items table
            'total_remaining' => Item::sum('remaining'),

            // Count of service records that require attention
            'item_service_required' => Service_Record::whereIn('status', ['scheduled', 'under repair', 'cancelled'])->count(),
        ];

        // Today’s counts
        $itemsAddedToday = Inventory::whereDate('created_at', $today)->count();
        $itemsDistributedToday = ItemDistribution::whereIn('status', ['distributed', 'issued', 'borrowed'])
            ->whereDate('created_at', $today)
            ->count();
        $servicesLoggedToday = Service_Record::whereDate('created_at', $today)->count();

        // Define your daily max for the progress bar (optional, or use max of today)
        $dailyMax = max($itemsAddedToday, $itemsDistributedToday, $servicesLoggedToday, 1); // avoid division by 0

        $overview = [
            'total_category' => Category::count(),
            'total_users' => User::count(),

            'items_added_today' => $itemsAddedToday,
            'items_distributed' => $itemsDistributedToday,
            'services_logged' => $servicesLoggedToday,

            'items_added_today_percentage' => round(($itemsAddedToday / $dailyMax) * 100),
            'items_distributed_percentage' => round(($itemsDistributedToday / $dailyMax) * 100),
            'services_logged_percentage' => round(($servicesLoggedToday / $dailyMax) * 100),
        ];

        return view('home.index', [
            'stats' => $stats,

            'overview' => $overview,

            'recent_activities' => $recent_activities,
        ]);
    }

    public function getItemByQrCode($code)
    {
        try {
            $qr = QR_Code::with('inventory.item.category', 'inventory.item.unit', 'inventory.qrCode')
                ->where('code', $code)
                ->first();

            if (!$qr) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code not found'
                ], 404);
            }

            $inventory = $qr->inventory;
            $item = $inventory->item ?? null;

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found for this QR code'
                ], 404);
            }

            $status = strtolower($inventory->status ?? 'unknown');

            $remainingQty = $item->type === 'consumable' ? $item->remaining ?? 0 : 1;

            $availableUnits = $item->type === 'consumable' ? [] : [
                [
                    'id' => $inventory->id,
                    'qr_code' => $inventory->qrCode->code ?? 'N/A',
                ]
            ];

            $activeDistribution = $inventory->itemDistributions()
                ->whereIn('status', ['issued', 'borrowed'])
                ->latest()
                ->first();


            $activeService = $inventory->serviceRecords()
                ->whereIn('status', ['scheduled', 'under repair'])
                ->latest()
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'category' => $item->category->name ?? 'N/A',
                    'type' => $item->type,
                    'unit' => $item->unit->name ?? 'N/A',
                    'supplier' => $item->supplier ?? 'N/A',
                    'remaining' => $remainingQty,
                    'units' => $availableUnits,
                    'inventory_id' => $inventory->id,
                    'status' => $status, 
                    'qr_code' => $qr->code,

                    'distribution_id' => $activeDistribution->id ?? null,
                    'borrower' => $activeDistribution->department_or_borrower ?? null,
                    'distribution_date' => $activeDistribution->distribution_date ?? null,

                    'service_record_id' => $activeService->id ?? null,
                    'schedule_date' => $activeService->service_date ?? null,
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
