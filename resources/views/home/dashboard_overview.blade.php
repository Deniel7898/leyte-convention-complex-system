<div class="row g-3 mt-2 align-items-stretch">

    <!-- Total Categories & Users -->
    <div class="col-lg-4 d-flex">
        <div class="mini-card w-100 d-flex flex-column">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">
                    <i class="bi bi-people me-2"></i>
                    Totals Overview
                </h5>
                <a href="{{ url('/activities') }}" class="analytics-link">
                    <i class="bi bi-graph-up"></i>
                    View Analytics
                </a>
            </div>

            <div class="flex-grow-1 d-flex flex-column justify-content-center">

                <div class="metric mb-3">
                    <span>Total Users</span>
                    <span class="fw-semibold">{{ $overview['total_users'] ?? 0 }}</span>
                </div>
                <div class="metric mb-3">
                    <span>Total Categories</span>
                    <span class="fw-semibold">{{ $overview['total_category'] ?? 0 }}</span>
                </div>

            </div>
        </div>
    </div>

    <!-- Recent Activity Metrics -->
    <div class="col-lg-4 d-flex">
        <div class="mini-card w-100 d-flex flex-column">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">
                    <i class="bi bi-bar-chart-line me-2"></i>
                    Today's Activity Overview
                </h5>
            </div>

            <div class="mt-1 flex-grow-1">

                <div class="metric">
                    <span>Items Added Today</span>
                    <span class="fw-semibold">{{ $overview['items_added_today'] ?? 0 }}</span>
                </div>
                <div class="progress mb-2" style="height:6px;">
                    <div class="progress-bar bg-primary"
                        style="width:{{ $overview['items_added_today_percentage'] ?? 0 }}%"></div>
                </div>

                <div class="metric">
                    <span>Items Distributed</span>
                    <span class="fw-semibold">{{ $overview['items_distributed'] ?? 0 }}</span>
                </div>
                <div class="progress mb-2" style="height:6px;">
                    <div class="progress-bar bg-success"
                        style="width:{{ $overview['items_distributed_percentage'] ?? 0 }}%"></div>
                </div>

                <div class="metric">
                    <span>Services Logged</span>
                    <span class="fw-semibold">{{ $overview['services_logged'] ?? 0 }}</span>
                </div>
                <div class="progress mb-2" style="height:6px;">
                    <div class="progress-bar bg-warning"
                        style="width:{{ $overview['services_logged_percentage'] ?? 0 }}%"></div>
                </div>

            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="col-lg-4 d-flex">
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