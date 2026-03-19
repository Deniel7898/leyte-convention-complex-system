@extends('layouts.app')

@section('content')

<style>
    /* =========================
   Stats Cards
========================== */
    .stats-card {
        border-radius: 16px;
        padding: 1.5rem 2rem;
        background-color: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        min-height: 110px;
    }

    .stats-card .stat-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 26px;
        color: #fff;
    }

    .stats-card.primary .stat-icon {
        background: #0d6efd;
    }

    .stats-card.success .stat-icon {
        background: #198754;
    }

    .stats-card.warning .stat-icon {
        background: #ffc107;
    }

    .stats-card.danger .stat-icon {
        background: #dc3545;
    }

    .stat-title {
        font-size: 0.95rem;
        color: #6c757d;
    }

    .stat-number {
        font-size: 1.6rem;
        font-weight: 700;
    }

    .stat-change {
        font-size: 0.85rem;
    }

    .stats-card .stat-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 26px;
        color: #fff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        /* Makes it clickable */
        text-decoration: none;
        /* Remove underline from <a> */
    }

    .stats-card .stat-icon:hover {
        transform: scale(1.1);
        /* Slightly enlarge on hover */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        /* Add shadow on hover */
    }

    /* =========================
   Quick Actions
========================== */
    .quick-action-box {
        border: 3px dashed #d6dbe1;
        border-radius: 14px;
        padding: 30px;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all .25s ease-in-out;
        display: flex;
        gap: 15px;
        align-items: flex-start;
        height: 100%;
    }

    .quick-action-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .quick-action-box.primary:hover {
        background: #e7ecf5;
        border-color: #0d6efd;
    }

    .quick-action-box.success:hover {
        background: #e6f4ea;
        border-color: #198754;
    }

    .quick-action-box.warning:hover {
        background: #fff8e6;
        border-color: #ffc107;
    }

    .quick-action-box .icon {
        font-size: 26px;
    }

    .action-title {
        font-weight: 600;
        font-size: 1.05rem;
        margin-bottom: 4px;
    }

    .action-desc {
        font-size: 0.9rem;
        color: #6c757d;
    }

    /* =========================
   Mini Cards
========================== */
    .mini-card {
        border-radius: 14px;
        padding: 20px;
        background-color: #f8f9fa;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        height: 100%;
    }

    .mini-card h5 {
        font-weight: 600;
        margin-bottom: 12px;
    }

    .metric {
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
        font-size: 0.95rem;
    }

    .status-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .status-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 0.95rem;
    }

    /* =========================
   Recent Activity Timeline
========================== */

    .activity-item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        /* reduced gap */
        padding: 10px 0;
        /* clean vertical spacing */
    }

    .activity-item:not(:last-child) {
        border-bottom: 1px solid #e9ecef;
        /* soft divider instead of <hr> */
    }

    .activity-content {
        flex-grow: 1;
    }

    .activity-dot {
        width: 8px;
        /* slightly smaller */
        height: 8px;
        border-radius: 50%;
        margin-top: 6px;
    }

    .activity-time {
        white-space: nowrap;
        font-size: 0.8rem;
        /* slightly smaller time */
    }
</style>


<!-- =========================
     Stats Cards
========================== -->
<div class="row g-3 mt-3">

    <div class="col-lg-3 col-md-6">
        <div class="stats-card primary">
            <div>
                <div class="stat-title">Total Items</div>
                <div class="stat-number">1,234</div>
            </div>
            <a href="{{ route('inventory.index') }}" class="stat-icon"><i class="bi bi-box-seam"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-card success">
            <div>
                <div class="stat-title">Available</div>
                <div class="stat-number">856</div>
            </div>
            <a href="{{ route('items.index') }}" class="stat-icon"><i class="bi bi-check-circle"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-card warning">
            <div>
                <div class="stat-title">Items Service Required</div>
                <div class="stat-number">45</div>
            </div>
            <a href="{{ route('service_records.index') }}" class="stat-icon"><i class="bi bi-tools"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-card danger">
            <div>
                <div class="stat-title">To Purchase</div>
                <div class="stat-number">23</div>
            </div>
            <a href="{{ route('purchase-requests.index') }}" class="stat-icon"><i class="bi bi-cart-dash"></i></a>
        </div>
    </div>

</div>


<!-- =========================
     Quick Actions
========================== -->
<div class="mt-4">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4 bg-light rounded-4">
            <h4 class="fw-semibold mb-4">Quick Actions</h4>

            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <a href="{{ route('items.index') }}" class="quick-action-box primary text-decoration-none">
                        <div class="icon text-primary">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <div class="action-title text-primary">Add New Item</div>
                            <div class="action-desc">Record new inventory</div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-4 col-md-6">
                    <a href="{{ route('item_distributions.index') }}" class="quick-action-box success text-decoration-none">
                        <div class="icon text-success">
                            <i class="bi bi-send"></i>
                        </div>
                        <div>
                            <div class="action-title text-success">Log Distribution</div>
                            <div class="action-desc">Track outgoing supplies</div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-4 col-md-6">
                    <a href="{{ route('service_records.index') }}" class="quick-action-box warning text-decoration-none">
                        <div class="icon text-warning">
                            <i class="bi bi-tools"></i>
                        </div>
                        <div>
                            <div class="action-title text-warning">Log Item Service</div>
                            <div class="action-desc">Track installation & maintenance</div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- =========================
     Recent Activity & System Status
========================== -->
<div class="row g-3 mt-3 align-items-stretch">

    <!-- Recent Activity Metrics -->
    <div class="col-lg-6 d-flex">
        <div class="mini-card w-100 d-flex flex-column">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">
                    <i class="bi bi-bar-chart-line me-2"></i>
                    Recent Activity Metrics
                </h5>
                <a href="{{ url('/activities') }}" class="analytics-link">
                    <i class="bi bi-graph-up"></i>
                    View Analytics
                </a>
            </div>

            <div class="mt-1 flex-grow-1">

                <div class="metric">
                    <span>Items Added Today</span>
                    <span class="fw-semibold">23</span>
                </div>
                <div class="progress mb-2" style="height:6px;">
                    <div class="progress-bar bg-primary" style="width:65%"></div>
                </div>

                <div class="metric">
                    <span>Items Distributed</span>
                    <span class="fw-semibold">15</span>
                </div>
                <div class="progress mb-2" style="height:6px;">
                    <div class="progress-bar bg-success" style="width:45%"></div>
                </div>

                <div class="metric">
                    <span>Services Logged</span>
                    <span class="fw-semibold">5</span>
                </div>
                <div class="progress mb-2" style="height:6px;">
                    <div class="progress-bar bg-warning" style="width:25%"></div>
                </div>

                <div class="metric">
                    <span>Items in Transit</span>
                    <span class="fw-semibold">12</span>
                </div>
                <div class="progress" style="height:6px;">
                    <div class="progress-bar bg-info" style="width:40%"></div>
                </div>

            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="col-lg-6 d-flex">
        <div class="mini-card w-100 d-flex flex-column">

            <h5 class="mb-3">
                <i class="bi bi-server me-2"></i>
                System Status
            </h5>

            <ul class="status-list flex-grow-1">

                <li>
                    <div><strong>Database</strong></div>
                    <span class="badge bg-success-subtle text-success">Online</span>
                </li>

                <li>
                    <div><strong>Local Sync</strong></div>
                    <span class="badge bg-primary-subtle text-primary">Syncing</span>
                </li>

                <li>
                    <div><strong>Backup Service</strong></div>
                    <span class="badge bg-warning-subtle text-warning">Processing</span>
                </li>

                <li>
                    <div>
                        <strong>Last Backup</strong><br>
                        <span class="small text-muted">Today • 2:45 PM</span>
                    </div>
                    <span class="badge bg-success-subtle text-success">Updated</span>
                </li>

            </ul>

        </div>
    </div>

</div>
<!-- =========================
     Recent Activity Timeline
========================== -->
<div class="mt-3"> <!-- reduced from mt-4 -->
    <div class="mini-card py-3"> <!-- reduced vertical padding -->

        <h5 class="mb-3"> <!-- reduced from mb-4 -->
            <i class="bi bi-clock-history me-2"></i>
            Recent Activity
        </h5>

        <div class="activity-item">
            <div class="activity-dot bg-success"></div>
            <div class="activity-content">
                <div class="fw-semibold">New delivery received</div>
                <div class="text-muted small">Office Chairs (x10)</div>
            </div>
            <div class="activity-time text-muted small">
                10 minutes ago
            </div>
        </div>

        <div class="activity-item">
            <div class="activity-dot bg-warning"></div>
            <div class="activity-content">
                <div class="fw-semibold">Maintenance required</div>
                <div class="text-muted small">Projector - Room 201</div>
            </div>
            <div class="activity-time text-muted small">
                1 hour ago
            </div>
        </div>

        <div class="activity-item">
            <div class="activity-dot bg-primary"></div>
            <div class="activity-content">
                <div class="fw-semibold">Item issued</div>
                <div class="text-muted small">Laptop - HP EliteBook</div>
            </div>
            <div class="activity-time text-muted small">
                2 hours ago
            </div>
        </div>

        <div class="activity-item">
            <div class="activity-dot bg-primary"></div>
            <div class="activity-content">
                <div class="fw-semibold">Purchase order created</div>
                <div class="text-muted small">Printer Cartridges</div>
            </div>
            <div class="activity-time text-muted small">
                3 hours ago
            </div>
        </div>

    </div>
</div>

@endsection


@push('scripts')
<script>
    function handleQuickAction(type) {
        switch (type) {
            case 'add':
                window.location.href = "{{ url('/items/create') }}";
                break;
            case 'distribution':
                window.location.href = "{{ url('/deliveries/create') }}";
                break;
            case 'service':
                window.location.href = "{{ url('/service-records') }}";
                break;
        }
    }
</script>
@endpush