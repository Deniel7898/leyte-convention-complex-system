@extends('layouts.app')

@section('content')

    <!-- =========================
         Stats Cards
    ========================== -->
    <div class="row g-3 mt-3">

        <div class="col-lg-3 col-md-6">
            <div class="stats-card primary">
                <div>
                    <div class="stat-title">Total Items</div>
                    <div class="stat-number">{{ number_format($total_stock) }}</div>
                </div>
                <a href="{{ route('inventory.index') }}" class="stat-icon"><i class="bi bi-box-seam"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card success">
                <div>
                    <div class="stat-title">Available</div>
                    <div class="stat-number">{{ number_format($total_remaining) }}</div>
                </div>
                <a href="{{ route('inventory.index') }}" class="stat-icon"><i class="bi bi-check-circle"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card warning">
                <div>
                    <div class="stat-title">Items Service Required</div>
                    <div class="stat-number">{{ number_format($item_service_required) }}</div>
                </div>
                <a href="{{ route('service_records.index') }}" class="stat-icon"><i class="bi bi-tools"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card danger">
                <div>
                    <div class="stat-title">To Purchase</div>
                    <div class="stat-number">(23)</div>
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
                <h4 class="fw-semibold mb-2">Quick Actions</h4>

                <!-- Restock / Distribute / Service Quick Actions -->
                <div class="row g-3 mt-2">
                    <div class="col-lg-4 col-md-6">
                        <div class="quick-action-box primary" data-action="restock" style="cursor:pointer;">
                            <div class="icon text-primary"><i class="bi bi-box-seam"></i></div>
                            <div>
                                <div class="action-title text-primary">Restock</div>
                                <div class="action-desc">Scan item to restock</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="quick-action-box success" data-action="distribute" style="cursor:pointer;">
                            <div class="icon text-success"><i class="bi bi-send"></i></div>
                            <div>
                                <div class="action-title text-success">Distribute</div>
                                <div class="action-desc">Scan item to distribute</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="quick-action-box warning" data-action="service" style="cursor:pointer;">
                            <div class="icon text-warning"><i class="bi bi-tools"></i></div>
                            <div>
                                <div class="action-title text-warning">Service</div>
                                <div class="action-desc">Scan item for service</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scan Modal -->
                <div class="modal fade" id="scanModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 shadow-lg">
                            <div class="modal-header bg-white border-0">
                                <h5 class="modal-title fw-bold" id="scanModalTitle">Add Stock</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <!-- Scanner Icon -->
                                <div style="font-size: 48px; color: #3b82f6; margin: 1rem 0;">
                                    <!-- Using SVG icon like in your screenshot -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" fill="none"
                                        stroke="#3b82f6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-target">
                                        <circle cx="36" cy="36" r="10" />
                                        <circle cx="36" cy="36" r="22" />
                                        <circle cx="36" cy="36" r="30" />
                                    </svg>
                                </div>

                                <p id="scanModalMessage" class="text-muted mb-3">Waiting for barcode scan...</p>

                                <input type="text" id="manualQrInput" class="form-control mb-3"
                                    placeholder="Or enter QR code manually" autocomplete="off" autofocus>

                                <button id="manualSubmit" class="btn w-100 text-white"
                                    style="background-color: rgb(43, 45, 87);">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Single Form Container -->
                <div id="actionFormContainer"
                    style="display:none; margin-top:20px; border:1px solid #ddd; padding:20px; border-radius:8px;">
                    <h5 id="actionTitle"></h5>
                    <form method="POST" id="actionForm">
                        @csrf
                        <!-- Hidden input for actual item_id -->
                        <input type="hidden" name="item_id" value="">

                        <!-- Visible input just for display -->
                        <div class="mb-3">
                            <label>Item</label>
                            <input type="text" class="form-control text-muted" value="" readonly>
                        </div>

                        <div class="mb-3" id="quantityGroup">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" id="quantity" name="quantity" class="form-control" min="1" required>
                        </div>

                        <div class="mb-3" id="notesGroup">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea id="notes" name="notes" class="form-control" rows="2"
                                placeholder="Optional notes"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" id="actionSubmit"></button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Modal elements
        const scanModalEl = document.getElementById('scanModal');
        const scanModal = new bootstrap.Modal(scanModalEl);
        const scanMessage = document.getElementById('scanModalMessage');
        const manualQrInput = document.getElementById('manualQrInput');
        const manualSubmit = document.getElementById('manualSubmit');

        // Form elements
        const formContainer = document.getElementById('actionFormContainer');
        const formTitle = document.getElementById('actionTitle');
        const actionForm = document.getElementById('actionForm');
        const quantityGroup = document.getElementById('quantityGroup');
        const notesGroup = document.getElementById('notesGroup');
        const actionSubmit = document.getElementById('actionSubmit');

        // Scanner state
        let scanning = false;
        let scanBuffer = '';
        let scanTimeout;
        let currentListener = null;
        let currentAction = '';

        // Action configuration
        const actionConfig = {
            restock: {
                title: 'Restock Item',
                formAction: '{{ route("inventory.add_stock") }}',
                showQuantity: true,
                showNotes: true,
                submitText: 'Restock'
            },
            distribute: {
                title: 'Distribute Item',
                formAction: '{{ route("item_distributions.store") }}',
                showQuantity: true,
                showNotes: true,
                submitText: 'Distribute'
            },
            service: {
                title: 'Log Item Service',
                formAction: '{{ route("service_records.store") }}',
                showQuantity: false,
                showNotes: true,
                submitText: 'Log Service'
            }
        };

        // Start scanning modal
        function startScan(actionKey) {
            if (scanning) return;
            scanning = true;
            currentAction = actionKey;

            // Update modal
            scanModalEl.querySelector('.modal-title').innerText = `Scan Item for ${actionConfig[actionKey].title}`;
            scanMessage.innerText = 'Waiting for QR scan...';
            manualQrInput.value = '';
            scanModal.show();
            manualQrInput.focus();

            // Remove previous listener if exists
            if (currentListener) {
                document.removeEventListener('keydown', currentListener);
                currentListener = null;
            }

            // Keydown listener for scanner input
            currentListener = function (e) {
                if (e.key.length === 1) scanBuffer += e.key;

                if (e.key === 'Enter') {
                    const code = scanBuffer.trim() || manualQrInput.value.trim();
                    if (!code) return;
                    scanBuffer = '';
                    fetchItem(code);
                }

                clearTimeout(scanTimeout);
                scanTimeout = setTimeout(() => scanBuffer = '', 100);
            };

            document.addEventListener('keydown', currentListener);

            // Manual input submit
            manualSubmit.onclick = () => {
                const code = manualQrInput.value.trim();
                if (!code) return;
                fetchItem(code);
            };
        }

        // Fetch item details from server
        function fetchItem(code) {
            fetch(`/home/qr/${encodeURIComponent(code)}`)
                .then(res => res.json())
                .then(result => {
                    if (!result.success) {
                        alert(result.message || 'Item not found');
                        resetScan();
                        return;
                    }

                    const item = result.data;
                    const config = actionConfig[currentAction];

                    // Populate form
                    formContainer.style.display = 'block';
                    formTitle.innerText = config.title;
                    actionForm.action = config.formAction;
                    actionForm.querySelector('input[name="item_id"]').value = item.item_id; // hidden
                    actionForm.querySelector('input[readonly]').value = item.item_name; // visible
                    actionForm.querySelector('#quantity').value = '';
                    actionForm.querySelector('#notes').value = '';
                    quantityGroup.style.display = config.showQuantity ? 'block' : 'none';
                    notesGroup.style.display = config.showNotes ? 'block' : 'none';
                    actionSubmit.innerText = config.submitText;

                    scanModal.hide();
                    resetScan();
                })
                .catch(err => {
                    console.error(err);
                    alert('Error fetching item details');
                    scanModal.hide();
                    resetScan();
                });
        }

        // Reset scanning state
        function resetScan() {
            scanning = false;
            scanBuffer = '';
            if (currentListener) {
                document.removeEventListener('keydown', currentListener);
                currentListener = null;
            }
        }

        // Reset when modal is closed manually
        scanModalEl.addEventListener('hidden.bs.modal', () => {
            manualQrInput.value = '';
            resetScan();
        });

        // Attach click events to quick action buttons
        document.querySelectorAll('.quick-action-box').forEach(box => {
            box.addEventListener('click', () => {
                const action = box.dataset.action.toLowerCase();
                if (['restock', 'distribute', 'service'].includes(action)) {
                    startScan(action);
                }
            });
        });
    </script>

    <div class="row g-3 mt-3 align-items-stretch">

        <!-- Total Categories & Users -->
        <div class="col-lg-4 d-flex">
            <div class="mini-card w-100 d-flex flex-column">

                <h5 class="mb-3">
                    <i class="bi bi-people me-2"></i>
                    Totals Overview
                </h5>

                <div class="flex-grow-1 d-flex flex-column justify-content-center">

                    <div class="metric mb-3">
                        <span>Total Categories</span>
                        <span class="fw-semibold">{{ $total_category ?? 0 }}</span>
                    </div>

                    <div class="metric mb-3">
                        <span>Total Users</span>
                        <span class="fw-semibold">{{ $total_users ?? 0 }}</span>
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
                        <span class="fw-semibold">{{ $items_added_today }}</span>
                    </div>
                    <div class="progress mb-2" style="height:6px;">
                        <div class="progress-bar bg-primary" style="width:{{ $items_added_today_percentage ?? 65 }}%"></div>
                    </div>

                    <div class="metric">
                        <span>Items Distributed</span>
                        <span class="fw-semibold">{{ $items_distributed }}</span>
                    </div>
                    <div class="progress mb-2" style="height:6px;">
                        <div class="progress-bar bg-success" style="width:{{ $items_distributed_percentage ?? 45 }}%"></div>
                    </div>

                    <div class="metric">
                        <span>Services Logged</span>
                        <span class="fw-semibold">{{ $services_logged }}</span>
                    </div>
                    <div class="progress mb-2" style="height:6px;">
                        <div class="progress-bar bg-warning" style="width:{{ $services_logged_percentage ?? 25 }}%"></div>
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

    <!-- =========================
         Recent Activity Timeline
    ========================== -->
    <div class="mt-3">
        <div class="mini-card py-3">
            <h5 class="mb-3">
                <i class="bi bi-clock-history me-2"></i>
                Recent Activity
            </h5>

            @foreach($recent_activities as $activity)
                        <div class="activity-item">
                            <!-- Set color based on type -->
                            <div class="activity-dot 
                    @if(in_array($activity->action, ['item created', 'added stock', 'added unit']))
                        bg-success

                    @elseif(in_array($activity->action, ['distributed', 'issued', 'installation']))
                        bg-primary

                    @elseif(in_array($activity->action, ['returned']))
                        bg-info

                    @elseif(in_array($activity->action, ['maintenance', 'borrowed', 'inspection']))
                        bg-warning

                    @elseif(in_array($activity->action, ['service completed']))
                        bg-dark

                    @elseif(in_array($activity->action, ['deleted']))
                        bg-danger

                    @else
                        bg-secondary
                    @endif
                "></div>

                            <div class="activity-content">
                                <div class="fw-semibold">{{ ucfirst($activity->action ?? '') }}</div>
                                <div class="text-muted small">{{ $activity->notes }}</div>
                            </div>

                            <div class="activity-time text-muted small">
                                {{ $activity->created_at->diffForHumans() }}
                            </div>
                        </div>
            @endforeach

        </div>
    </div>

@endsection