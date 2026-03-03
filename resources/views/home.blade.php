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

    .stat-change.up {
        color: #198754;
    }

    .stat-change.down {
        color: #dc3545;
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
        transition: all 0.25s ease-in-out;
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
        background-color: #e7ecf5;
        border-color: #0d6efd;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .quick-action-box.success:hover {
        background-color: #e6f4ea;
        border-color: #198754;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .quick-action-box.warning:hover {
        background-color: #fff8e6;
        border-color: #ffc107;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
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
        margin-bottom: 15px;
    }

    .metric {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .status-list {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }

    .status-list li {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        font-size: 0.95rem;
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
                <div class="stat-change up">+5.2% vs last month</div>
            </div>
            <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-card success">
            <div>
                <div class="stat-title">Available</div>
                <div class="stat-number">856</div>
                <div class="stat-change up">+3.8% vs last month</div>
            </div>
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-card warning">
            <div>
                <div class="stat-title">Needs Maintenance</div>
                <div class="stat-number">45</div>
                <div class="stat-change up">+2.1% vs last month</div>
            </div>
            <div class="stat-icon"><i class="bi bi-tools"></i></div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-card danger">
            <div>
                <div class="stat-title">To Purchase</div>
                <div class="stat-number">23</div>
                <div class="stat-change down">-1.2% vs last month</div>
            </div>
            <div class="stat-icon"><i class="bi bi-cart-dash"></i></div>
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
                    <div class="quick-action-box primary"
                        onclick="handleQuickAction('add')">
                        <div class="icon text-primary">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <div class="action-title">Add New Item</div>
                            <div class="action-desc">Record new inventory</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="quick-action-box success"
                        onclick="handleQuickAction('distribution')">
                        <div class="icon text-success">
                            <i class="bi bi-send"></i>
                        </div>
                        <div>
                            <div class="action-title">Log Distribution</div>
                            <div class="action-desc">Track outgoing supplies</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="quick-action-box warning"
                        onclick="handleQuickAction('service')">
                        <div class="icon text-warning">
                            <i class="bi bi-tools"></i>
                        </div>
                        <div>
                            <div class="action-title">Log Item Service</div>
                            <div class="action-desc">
                                Track installation & maintenance
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- =========================
     Recent Activity & Status
========================== -->
<div class="row g-3 mt-2 align-items-stretch">

    <!-- Recent Activity -->
    <div class="col-lg-6">
        <div class="mini-card h-100">

            <h5 class="mb-2">
                <i class="bi bi-bar-chart-line me-2"></i>
                Recent Activity Metrics
            </h5>

            <a href="{{ url('/activities') }}"
                class="text-decoration-none small">
                View All Activities →
            </a>

            <div class="mt-3">

                <!-- Items Added -->
                <div class="metric mb-1">
                    <span>Items Added Today</span>
                    <span class="fw-semibold">23</span>
                </div>
                <div class="progress mb-2" style="height: 6px;">
                    <div class="progress-bar bg-primary" style="width: 65%"></div>
                </div>

                <!-- Items Distributed -->
                <div class="metric mb-1">
                    <span>Items Distributed</span>
                    <span class="fw-semibold">15</span>
                </div>
                <div class="progress mb-2" style="height: 6px;">
                    <div class="progress-bar bg-success" style="width: 45%"></div>
                </div>

                <!-- Services Logged -->
                <div class="metric mb-1">
                    <span>Services Logged</span>
                    <span class="fw-semibold">5</span>
                </div>
                <div class="progress mb-2" style="height: 6px;">
                    <div class="progress-bar bg-warning" style="width: 25%"></div>
                </div>

                <!-- Items in Transit -->
                <div class="metric mb-1">
                    <span>Items in Transit</span>
                    <span class="fw-semibold">12</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-info" style="width: 40%"></div>
                </div>

            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="col-lg-6">
        <div class="mini-card h-100">

            <h5 class="mb-2">
                <i class="bi bi-server me-2"></i>
                System Status
            </h5>

            <ul class="status-list mt-3 p-0">

                <li class="d-flex justify-content-between align-items-center py-2">
                    <div>
                        <strong>Database</strong><br>
                        <span class="fw-semibold small">98%</span>
                    </div>
                    <span class="badge bg-success">Online</span>
                </li>

                <li class="d-flex justify-content-between align-items-center py-2">
                    <div>
                        <strong>Local Sync</strong><br>
                        <span class="fw-semibold small">76%</span>
                    </div>
                    <span class="badge bg-primary">Syncing</span>
                </li>

                <li class="d-flex justify-content-between align-items-center py-2">
                    <div>
                        <strong>Backup Service</strong><br>
                        <span class="fw-semibold small">64%</span>
                    </div>
                    <span class="badge bg-warning text-dark">Processing</span>
                </li>

                <li class="d-flex justify-content-between align-items-center py-2">
                    <div>
                        <strong>Last Backup</strong><br>
                        <span class="fw-semibold small text-muted">
                            Today • 2:45 PM

                    </div>
                    <span class="badge bg-secondary">Updated</span>
                </li>

            </ul>

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