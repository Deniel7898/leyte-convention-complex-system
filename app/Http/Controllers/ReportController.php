<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : null;

        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : null;

        $applyDateRange = function ($query, string $column) use ($dateFrom, $dateTo) {
            return $query
                ->when($dateFrom, fn ($q) => $q->whereDate($column, '>=', $dateFrom->toDateString()))
                ->when($dateTo, fn ($q) => $q->whereDate($column, '<=', $dateTo->toDateString()));
        };

        // =========================
        // SUMMARY CARDS
        // =========================
        $totalItems = DB::table('items')
            ->whereNull('deleted_at')
            ->count();

        $totalStock = DB::table('items')
            ->whereNull('deleted_at')
            ->sum('total_stock');

        $remainingStock = DB::table('items')
            ->whereNull('deleted_at')
            ->sum('remaining');

        // =========================
        // LOW STOCK ITEMS
        // =========================
        $lowStockItems = DB::table('items as i')
            ->leftJoin('categories as c', 'c.id', '=', 'i.category_id')
            ->leftJoin('units as u', 'u.id', '=', 'i.unit_id')
            ->whereNull('i.deleted_at')
            ->select(
                'i.name',
                'i.type',
                'i.remaining',
                'i.total_stock',
                'c.name as category_name',
                'u.name as unit_name'
            )
            ->orderBy('i.remaining', 'asc')
            ->get()
            ->filter(fn ($item) => (int) $item->remaining <= 5)
            ->values();

        // =========================
        // PURCHASE REQUESTS
        // =========================
        $purchaseRequestsBase = DB::table('purchase_request')
            ->whereNull('deleted_at');

        $purchaseRequestsBase = $applyDateRange($purchaseRequestsBase, 'request_date');

        $purchaseRequests = $purchaseRequestsBase
            ->orderByDesc('request_date')
            ->orderByDesc('id')
            ->get([
                'id',
                'request_date',
                'status',
                'items',
                'created_at',
            ]);

        $purchaseRequestCount = $purchaseRequests->count();
        $pendingPurchaseRequests = $purchaseRequests->where('status', 'pending')->count();

        $purchaseRequestStatusSummary = $applyDateRange(
            DB::table('purchase_request')->whereNull('deleted_at'),
            'request_date'
        )
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $requestedItemTotals = [];
        $totalRequestedQuantity = 0;

        $recentPurchaseRequests = $purchaseRequests->take(10)->map(function ($pr) use (&$requestedItemTotals, &$totalRequestedQuantity) {
            $items = json_decode($pr->items ?? '[]', true);

            if (!is_array($items)) {
                $items = [];
            }

            $itemCount = count($items);
            $requestTotalQty = 0;

            foreach ($items as $item) {
                $qty = (int) ($item['quantity'] ?? 0);
                $name = trim((string) ($item['item_name'] ?? 'Unknown Item'));

                $requestTotalQty += $qty;
                $totalRequestedQuantity += $qty;

                if (!isset($requestedItemTotals[$name])) {
                    $requestedItemTotals[$name] = 0;
                }

                $requestedItemTotals[$name] += $qty;
            }

            return (object) [
                'id' => $pr->id,
                'request_date' => $pr->request_date,
                'status' => $pr->status,
                'item_count' => $itemCount,
                'total_quantity' => $requestTotalQty,
                'items' => $items,
            ];
        });

        foreach ($purchaseRequests->skip(10) as $pr) {
            $items = json_decode($pr->items ?? '[]', true);

            if (!is_array($items)) {
                $items = [];
            }

            foreach ($items as $item) {
                $qty = (int) ($item['quantity'] ?? 0);
                $name = trim((string) ($item['item_name'] ?? 'Unknown Item'));

                $totalRequestedQuantity += $qty;

                if (!isset($requestedItemTotals[$name])) {
                    $requestedItemTotals[$name] = 0;
                }

                $requestedItemTotals[$name] += $qty;
            }
        }

        $topRequestedItems = collect($requestedItemTotals)
            ->map(function ($quantity, $name) {
                return [
                    'name' => $name,
                    'quantity' => $quantity,
                ];
            })
            ->sortByDesc('quantity')
            ->values()
            ->take(10);

        // =========================
        // INVENTORY STATUS SUMMARY
        // =========================
        $inventoryStatusSummary = DB::table('inventories')
            ->whereNull('deleted_at')
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $inventoryRecordCount = $inventoryStatusSummary->sum('total');

        // =========================
        // DISTRIBUTION SUMMARY
        // =========================
        $distributionSummary = $applyDateRange(
            DB::table('item_distributions')->whereNull('deleted_at'),
            'distribution_date'
        )
            ->select(
                'type',
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('COALESCE(SUM(quantity), 0) as total_quantity')
            )
            ->groupBy('type')
            ->orderBy('type')
            ->get();

        $distributionTransactionCount = $distributionSummary->sum('total_transactions');
        $distributedQuantity = $distributionSummary->sum('total_quantity');

        // =========================
        // SERVICE SUMMARY
        // =========================
        $serviceStatusSummary = $applyDateRange(
            DB::table('service_records')->whereNull('deleted_at'),
            'service_date'
        )
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $serviceRecordCount = $serviceStatusSummary->sum('total');

        return view('reports.index', [
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,

            'totalItems' => $totalItems,
            'totalStock' => $totalStock,
            'remainingStock' => $remainingStock,
            'lowStockItems' => $lowStockItems,

            'purchaseRequestCount' => $purchaseRequestCount,
            'pendingPurchaseRequests' => $pendingPurchaseRequests,
            'purchaseRequestStatusSummary' => $purchaseRequestStatusSummary,
            'recentPurchaseRequests' => $recentPurchaseRequests,
            'topRequestedItems' => $topRequestedItems,
            'totalRequestedQuantity' => $totalRequestedQuantity,

            'inventoryStatusSummary' => $inventoryStatusSummary,
            'inventoryRecordCount' => $inventoryRecordCount,

            'distributionSummary' => $distributionSummary,
            'distributionTransactionCount' => $distributionTransactionCount,
            'distributedQuantity' => $distributedQuantity,

            'serviceStatusSummary' => $serviceStatusSummary,
            'serviceRecordCount' => $serviceRecordCount,
        ]);
    }
}