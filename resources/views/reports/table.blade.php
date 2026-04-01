<div class="reports-grid">

    {{-- CHARTS --}}
    <div class="report-panel span-6 no-print">
        <div class="report-panel-header">
            <h3 class="report-panel-title">Purchase Request Status Chart</h3>
            <div class="report-panel-subtitle">Visual summary by request status</div>
        </div>
        <div class="report-panel-body">
            <div class="chart-wrap">
                <canvas id="prStatusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="report-panel span-6 no-print">
        <div class="report-panel-header">
            <h3 class="report-panel-title">Inventory Status Chart</h3>
            <div class="report-panel-subtitle">Current inventory record breakdown</div>
        </div>
        <div class="report-panel-body">
            <div class="chart-wrap">
                <canvas id="inventoryStatusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="report-panel span-6 no-print">
        <div class="report-panel-header">
            <h3 class="report-panel-title">Distribution Quantity Chart</h3>
            <div class="report-panel-subtitle">Released quantity by transaction type</div>
        </div>
        <div class="report-panel-body">
            <div class="chart-wrap">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>

    <div class="report-panel span-6 no-print">
        <div class="report-panel-header">
            <h3 class="report-panel-title">Service Status Chart</h3>
            <div class="report-panel-subtitle">Service record distribution</div>
        </div>
        <div class="report-panel-body">
            <div class="chart-wrap">
                <canvas id="serviceStatusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="report-panel span-12 no-print">
        <div class="report-panel-header">
            <h3 class="report-panel-title">Top Requested Items Chart</h3>
            <div class="report-panel-subtitle">Most requested items from purchase requests</div>
        </div>
        <div class="report-panel-body">
            <div class="chart-wrap">
                <canvas id="topRequestedChart"></canvas>
            </div>
        </div>
    </div>

    {{-- TABLES --}}
    <div class="report-panel span-6">
        <div class="report-panel-header">
            <h3 class="report-panel-title">Purchase Request Status</h3>
            <div class="report-panel-subtitle">Summary by request status</div>
        </div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchaseRequestStatusSummary as $row)
                    <tr>
                        <td>
                            <span class="status-badge status-{{ \Illuminate\Support\Str::slug($row->status, '-') }}">
                                {{ $row->status }}
                            </span>
                        </td>
                        <td>{{ number_format($row->total) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="empty-state">No purchase request data found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="report-panel span-6">
        <div class="report-panel-header">
            <h3 class="report-panel-title">Inventory Status</h3>
            <div class="report-panel-subtitle">Availability and usage summary</div>
        </div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventoryStatusSummary as $row)
                    <tr>
                        <td>
                            <span class="status-badge status-{{ \Illuminate\Support\Str::slug($row->status, '-') }}">
                                {{ $row->status }}
                            </span>
                        </td>
                        <td>{{ number_format($row->total) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="empty-state">No inventory status data found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="report-panel span-6">
        <div class="report-panel-header">
            <h3 class="report-panel-title">Distribution Summary</h3>
            <div class="report-panel-subtitle">Grouped by transaction type</div>
        </div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Transactions</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @forelse($distributionSummary as $row)
                    <tr>
                        <td>
                            <span class="status-badge status-{{ \Illuminate\Support\Str::slug($row->type, '-') }}">
                                {{ $row->type }}
                            </span>
                        </td>
                        <td>{{ number_format($row->total_transactions) }}</td>
                        <td>{{ number_format($row->total_quantity) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty-state">No distribution data found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="report-panel span-6">
        <div class="report-panel-header">
            <h3 class="report-panel-title">Service Summary</h3>
            <div class="report-panel-subtitle">Grouped by service status</div>
        </div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($serviceStatusSummary as $row)
                    <tr>
                        <td>
                            <span class="status-badge status-{{ \Illuminate\Support\Str::slug($row->status, '-') }}">
                                {{ $row->status }}
                            </span>
                        </td>
                        <td>{{ number_format($row->total) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="empty-state">No service record data found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="report-panel span-12">
        <div class="report-panel-header">
            <h3 class="report-panel-title">Recent Purchase Requests</h3>
            <div class="report-panel-subtitle">Latest requests with JSON item details</div>
        </div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>PR #</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Item Count</th>
                    <th>Total Qty</th>
                    <th>Items</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentPurchaseRequests as $row)
                    <tr>
                        <td>#{{ $row->id }}</td>
                        <td>{{ $row->request_date ? \Carbon\Carbon::parse($row->request_date)->format('M d, Y') : '—' }}</td>
                        <td>
                            <span class="status-badge status-{{ \Illuminate\Support\Str::slug($row->status, '-') }}">
                                {{ $row->status }}
                            </span>
                        </td>
                        <td>{{ number_format($row->item_count) }}</td>
                        <td>{{ number_format($row->total_quantity) }}</td>
                        <td>
                            @if(!empty($row->items))
                                <div class="mini-pill-wrap">
                                    @foreach($row->items as $item)
                                        <span class="mini-pill">
                                            <strong>{{ $item['item_name'] ?? 'Unknown Item' }}</strong>
                                            <span>x{{ $item['quantity'] ?? 0 }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span>No items</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">No purchase request records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="report-panel span-6">
        <div class="report-panel-header">
            <h3 class="report-panel-title">Top Requested Items</h3>
            <div class="report-panel-subtitle">Most requested items from PR records</div>
        </div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Total Requested Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topRequestedItems as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ number_format($item['quantity']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="empty-state">No requested item data found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="report-panel span-6">
        <div class="report-panel-header">
            <h3 class="report-panel-title">Low Stock Items</h3>
            <div class="report-panel-subtitle">Items with remaining stock of 5 or below</div>
        </div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Remaining</th>
                    <th>Total Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lowStockItems as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category_name ?? '—' }}</td>
                        <td>{{ $item->type ?? '—' }}</td>
                        <td class="{{ (int)$item->remaining <= 2 ? 'text-danger' : '' }}">
                            {{ number_format($item->remaining) }}
                        </td>
                        <td>{{ number_format($item->total_stock) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">No low stock items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>