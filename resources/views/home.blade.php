@extends('layouts.app')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
            <!-- Restock Form Modal -->
            <div class="modal fade" id="actionFormModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content rounded-4 shadow-lg">

                        <!-- Modal Header -->
                        <div class="modal-header" style="background-color: rgb(43, 45, 87);">
                            <h5 class="modal-title text-white" id="actionTitle">Restock Item</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body">
                            <form action="{{ route('inventory.add_stock') }}" method="POST" enctype="multipart/form-data" id="restockItemForm">
                                @csrf

                                <input type="hidden" name="item_id" value="">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Category</label>
                                        <input type="text" class="form-control text-muted" value="" readonly id="itemCategory">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Item</label>
                                        <input type="text" class="form-control text-muted" value="" readonly id="itemName">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Type</label>
                                        <input type="text" class="form-control text-muted" value="" readonly id="itemType">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Unit</label>
                                        <input type="text" class="form-control text-muted" value="" readonly id="itemUnit">
                                    </div>

                                    <div class="col-md-6 mb-3" id="quantityGroup">
                                        <label for="quantity" class="form-label required">Quantity</label>
                                        <input type="number" id="quantity" name="quantity" class="form-control"
                                            min="1" maxlength="3" pattern="\d{1,3}"
                                            placeholder="Enter Quantity (max-999)">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Supplier</label>
                                        <input type="text" class="form-control text-muted" value="" readonly id="itemSupplier">
                                    </div>

                                    <div class="mb-3" id="notesGroup">
                                        <label for="stock-notes" class="form-label bold-label">Notes</label>
                                        <textarea id="stock-notes" name="notes" class="form-control" rows="1"
                                            style="resize: none; overflow-y: auto; max-height: 80px;"
                                            placeholder="Optional notes"></textarea>
                                    </div>
                                </div>

                                <!-- Modal Footer -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);" id="actionSubmitBtn">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Distribution Modal -->
            <div class="modal fade" id="itemDistributionModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content rounded-4 shadow-lg">
                        <form action="{{ route('item_distributions.store') }}" method="POST" enctype="multipart/form-data" id="itemDistributionForm">
                            @csrf

                            <div class="modal-header" style="background-color: rgb(43, 45, 87);">
                                <h5 class="modal-title text-white">New Distribution</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body p-4 bg-white rounded">
                                <!-- Hidden Inputs -->
                                <input type="hidden" name="item_id" id="distributionItemId" value="">
                                <input type="hidden" name="distribution_id" id="distributionId" value="">

                                <div class="row">
                                    <!-- Item Name -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Item</label>
                                        <input type="text" class="form-control text-muted" id="distributionItemName" readonly>
                                        <small class="text-muted" id="distributionItemRemaining">Available: 0</small>
                                    </div>

                                    <!-- Type -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Type</label>
                                        <select class="form-select" name="type" id="distributionType" required>
                                            <option value="">-- Select type --</option>
                                            <option value="distributed">Distributed</option>
                                            <option value="borrowed">Borrowed</option>
                                            <option value="issued">Issued</option>
                                        </select>
                                    </div>

                                    <!-- Quantity (consumable) -->
                                    <div class="col-md-6 mb-3" id="distributionQuantityWrapper" style="display:none;">
                                        <label class="form-label required">Quantity</label>
                                        <input type="number" class="form-control" name="quantity" id="distributionQuantity" min="1" value="1">
                                    </div>

                                    <!-- Units Table (non-consumable) -->
                                    <div class="col-12 mb-3" id="distributionUnitsWrapper" style="display:none;">
                                        <label class="form-label required">Select Units</label>
                                        <div class="border rounded shadow-sm" style="max-height: 200px; overflow-y:auto; background-color:#f9f9f9;">
                                            <table class="table table-sm mb-0 text-center align-middle">
                                                <thead class="table-light sticky-top">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>QR Code</th>
                                                        <th>
                                                            <input type="checkbox" id="selectAllDistributionUnits" title="Select/Deselect All">
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="distributionUnitsTableBody">
                                                    <!-- Rows added dynamically via JS -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Department/Borrower -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Department / Borrower</label>
                                        <input type="text" class="form-control" name="department_or_borrower" placeholder="Enter department or borrower" required>
                                    </div>

                                    <!-- Distribution Date -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Distribution Date</label>
                                        <input type="date" class="form-control" name="distribution_date" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}">
                                    </div>

                                    <!-- Due Date -->
                                    <div class="mb-3" id="distributionDueDateWrapper" style="display:none;">
                                        <label class="form-label required">Due Date</label>
                                        <input type="date" class="form-control" name="due_date" min="{{ date('Y-m-d') }}">
                                    </div>

                                    <!-- Notes -->
                                    <div class="mb-3">
                                        <label class="form-label bold-label">Notes</label>
                                        <textarea class="form-control" name="notes" rows="2" placeholder="Optional notes"></textarea>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Save Distribution</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Service Form Modal -->
            <div class="modal fade" id="serviceRecordModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content rounded-4 shadow-lg">
                        <form action="{{ route('service_records.store') }}" method="POST" enctype="multipart/form-data" id="serviceRecordForm">
                            @csrf

                            <!-- Modal Header -->
                            <div class="modal-header" style="background-color: rgb(43, 45, 87);">
                                <h5 class="modal-title text-white">New Item Service</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body">
                                <input type="hidden" name="item_id" id="serviceItemId" value="">
                                <input type="hidden" name="service_id" id="serviceId" value="">
                                <input type="hidden" name="page" value="inventory">

                                <div class="row">
                                    <!-- Item Info -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Item</label>
                                        <input type="text" class="form-control text-muted" id="serviceItemName" readonly>
                                        <small class="text-muted" id="serviceItemAvailable">Available: 0</small>
                                    </div>

                                    <!-- Service Type -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Service Type</label>
                                        <select class="form-select" id="serviceType" name="type" required>
                                            <option value="">-- Select type --</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="installation">Installation</option>
                                            <option value="inspection">Inspection</option>
                                        </select>
                                    </div>

                                    <!-- Technician -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Technician</label>
                                        <input type="text" class="form-control" id="technician" name="technician" required>
                                    </div>

                                    <!-- Service Date -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Schedule Date</label>
                                        <input type="date" class="form-control" id="serviceDate" name="service_date"
                                            value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-3">
                                        <label class="form-label required">Service Description</label>
                                        <textarea class="form-control" id="serviceDescription" name="description" rows="2" required></textarea>
                                    </div>

                                    <!-- Picture Upload -->
                                    <div class="mb-3">
                                        <label class="form-label bold-label">Service Picture</label>
                                        <div class="border rounded p-3 text-center" id="serviceDropzone" style="cursor:pointer; min-height:150px; display:flex; align-items:center; justify-content:center;">
                                            <input type="file" id="servicePicture" name="picture" accept="image/*" style="display:none;">
                                            <img id="servicePicturePreview" class="img-fluid rounded" style="max-height:120px; display:none;">
                                            <div id="servicePicturePlaceholder" class="text-muted">Click or drag to upload picture</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Save Service Record</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>  

        </div>
    </div>
</div>

<script>
    // ----------------------------
    // Modals
    // ----------------------------
    const scanModalEl = document.getElementById('scanModal');
    const scanModal = new bootstrap.Modal(scanModalEl);

    const restockModalEl = document.getElementById('actionFormModal'); // for restock
    const restockModal = new bootstrap.Modal(restockModalEl);

    const serviceModalEl = document.getElementById('serviceRecordModal');
    const serviceModal = new bootstrap.Modal(serviceModalEl);

    const distributionModalEl = document.getElementById('itemDistributionModal');
    const distributionModal = new bootstrap.Modal(distributionModalEl);

    // Scanner & manual input
    const scanMessage = document.getElementById('scanModalMessage');
    const manualQrInput = document.getElementById('manualQrInput');
    const manualSubmit = document.getElementById('manualSubmit');

    // Scanner state
    let scanning = false;
    let scanBuffer = '';
    let scanTimeout;
    let currentListener = null;
    let currentAction = '';

    // ----------------------------
    // Quick action buttons
    // ----------------------------
    document.querySelectorAll('.quick-action-box').forEach(box => {
        box.addEventListener('click', () => {
            const action = box.dataset.action.toLowerCase();
            if (['restock', 'distribute', 'service'].includes(action)) {
                startScan(action);
            }
        });
    });

    // ----------------------------
    // Start scanning
    // ----------------------------
    function startScan(actionKey) {
        if (scanning) return;
        scanning = true;
        currentAction = actionKey;

        scanModalEl.querySelector('.modal-title').innerText =
            `Scan Item for ${actionKey.charAt(0).toUpperCase() + actionKey.slice(1)}`;
        scanMessage.innerText = 'Waiting for QR scan...';
        manualQrInput.value = '';
        scanModal.show();
        manualQrInput.focus();

        if (currentListener) document.removeEventListener('keydown', currentListener);

        currentListener = function(e) {
            if (e.key.length === 1) scanBuffer += e.key;

            if (e.key === 'Enter') {
                const code = scanBuffer.trim() || manualQrInput.value.trim();
                if (!code) return;
                scanBuffer = '';
                fetchItem(code, actionKey);
            }

            clearTimeout(scanTimeout);
            scanTimeout = setTimeout(() => scanBuffer = '', 100);
        };

        document.addEventListener('keydown', currentListener);

        manualSubmit.onclick = () => {
            const code = manualQrInput.value.trim();
            if (!code) return;
            fetchItem(code, actionKey);
        };
    }

    // ----------------------------
    // Fetch item data
    // ----------------------------
    function fetchItem(code, actionKey) {
        fetch(`/home/qr/${encodeURIComponent(code)}`)
            .then(res => res.json())
            .then(result => {
                if (!result.success) {
                    alert(result.message || 'Item not found');
                    resetScan();
                    return;
                }

                const item = result.data;

                if (actionKey === 'distribute') {
                    populateDistributionModal(item);
                } else if (actionKey === 'restock') {
                    populateRestockModal(item);
                } else if (actionKey === 'service') {
                    populateServiceModal(item);
                }

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

    // ----------------------------
    // Reset scanning
    // ----------------------------
    function resetScan() {
        scanning = false;
        scanBuffer = '';
        if (currentListener) {
            document.removeEventListener('keydown', currentListener);
            currentListener = null;
        }
    }

    scanModalEl.addEventListener('hidden.bs.modal', () => {
        manualQrInput.value = '';
        resetScan();
    });

    // ----------------------------
    // Populate Restock Modal
    // ----------------------------
    function populateRestockModal(item) {
        if (item.type !== 'consumable') {
            alert('Restock is only allowed for consumable items.');
            return;
        }

        restockModalEl.querySelector('input[name="item_id"]').value = item.item_id || '';
        document.getElementById('itemName').value = item.item_name || '';
        document.getElementById('itemCategory').value = item.category || '';
        document.getElementById('itemType').value = item.type || '';
        document.getElementById('itemUnit').value = item.unit || '';
        document.getElementById('itemSupplier').value = item.supplier || '';
        document.getElementById('quantity').value = '';
        document.getElementById('stock-notes').value = '';

        document.getElementById('quantityGroup').style.display = 'block';
        document.getElementById('notesGroup').style.display = 'block';
        document.getElementById('actionSubmitBtn').innerText = 'Restock';

        restockModal.show();
    }

    // ----------------------------
    // Populate Service Modal
    // ----------------------------
    function populateServiceModal(item) {
        if (!item || !item.item_id) {
            alert('Item not found for service.');
            return;
        }

        serviceModalEl.querySelector('input[name="item_id"]').value = item.item_id || '';
        document.getElementById('serviceItemName').value = item.item_name || '';
        document.getElementById('serviceItemAvailable').innerText = `Available: ${item.remaining || 0}`;
        document.getElementById('technician').value = '';
        document.getElementById('serviceDescription').value = '';
        document.getElementById('serviceDate').value = new Date().toISOString().slice(0, 10);

        // Reset picture preview
        const preview = document.getElementById('servicePicturePreview');
        const placeholder = document.getElementById('servicePicturePlaceholder');
        const fileInput = document.getElementById('servicePicture');
        preview.style.display = 'none';
        placeholder.style.display = 'block';
        fileInput.value = '';

        serviceModal.show();
    }

    // ----------------------------
    // Distribution Modal
    // ----------------------------
    function populateDistributionModal(item) {
        const typeSelect = document.getElementById('distributionType');
        const typeWrapper = typeSelect.closest('.col-md-6.mb-3');
        const quantityWrapper = document.getElementById('distributionQuantityWrapper');
        const quantityInput = document.getElementById('distributionQuantity');
        const unitsWrapper = document.getElementById('distributionUnitsWrapper');
        const unitsTableBody = document.getElementById('distributionUnitsTableBody');
        const dueDateWrapper = document.getElementById('distributionDueDateWrapper');

        quantityWrapper.style.display = 'none';
        unitsWrapper.style.display = 'none';
        dueDateWrapper.style.display = 'none';
        unitsTableBody.innerHTML = '';
        quantityInput.value = 1;
        typeSelect.value = '';
        typeWrapper.style.display = 'block';

        document.getElementById('distributionItemId').value = item.item_id || '';
        document.getElementById('distributionItemName').value = item.item_name || '';
        document.getElementById('distributionItemRemaining').innerText = `Available: ${item.remaining || 0}`;

        if (item.type === 'consumable') {
            typeSelect.value = 'distributed';
            typeWrapper.style.display = 'none';
            if (item.remaining > 0) {
                quantityWrapper.style.display = 'block';
                setupDistributionQuantity(item.remaining);
            }
        } else {
            if (item.units && item.units.length > 0) {
                unitsWrapper.style.display = 'block';
                item.units.forEach((unit, index) => {
                    unitsTableBody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${unit.qr_code}</td>
                        <td><input type="checkbox" class="unitCheckbox" name="inventory_ids[]" value="${unit.id}"></td>
                    </tr>`;
                });
            }
            typeSelect.onchange = () => handleDistributionTypeChange(typeSelect.value, item.remaining);
        }

        distributionModal.show();
    }

    // ----------------------------
    // Handle Distribution Type
    // ----------------------------
    function handleDistributionTypeChange(type, remaining) {
        const quantityWrapper = document.getElementById('distributionQuantityWrapper');
        const unitsWrapper = document.getElementById('distributionUnitsWrapper');
        const dueDateWrapper = document.getElementById('distributionDueDateWrapper');
        const quantityInput = document.getElementById('distributionQuantity');

        quantityWrapper.style.display = 'none';
        unitsWrapper.style.display = 'none';
        dueDateWrapper.style.display = 'none';
        quantityInput.value = 1;

        switch (type) {
            case 'distributed':
                if (remaining > 0) {
                    quantityWrapper.style.display = 'block';
                    setupDistributionQuantity(remaining);
                }
                break;
            case 'borrowed':
                dueDateWrapper.style.display = 'block';
                if (remaining > 0) {
                    quantityWrapper.style.display = 'block';
                    setupDistributionQuantity(remaining);
                }
                break;
            case 'issued':
                unitsWrapper.style.display = unitsWrapper.children.length > 0 ? 'block' : 'none';
                break;
        }
    }

    // ----------------------------
    // Distribution Quantity Limit
    // ----------------------------
    function setupDistributionQuantity(remaining) {
        const input = document.getElementById('distributionQuantity');
        if (!input) return;
        input.setAttribute('max', remaining);
        input.value = remaining > 0 ? 1 : 0;
        input.addEventListener('input', function() {
            let value = Number(this.value);
            if (isNaN(value) || value < 1) this.value = 1;
            else if (value > remaining) this.value = remaining;
        });
    }

    // ----------------------------
    // Select/Deselect All Units
    // ----------------------------
    document.addEventListener('change', e => {
        if (e.target && e.target.id === 'selectAllDistributionUnits') {
            document.querySelectorAll('#distributionUnitsTableBody .unitCheckbox')
                .forEach(cb => cb.checked = e.target.checked);
        }
    });

    // ----------------------------
    // Service Picture Dropzone
    // ----------------------------
    const dropzone = document.getElementById('serviceDropzone');
    const fileInput = document.getElementById('servicePicture');
    const preview = document.getElementById('servicePicturePreview');
    const placeholder = document.getElementById('servicePicturePlaceholder');

    dropzone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(fileInput.files[0]);
        } else {
            preview.style.display = 'none';
            placeholder.style.display = 'block';
        }
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

        <!-- Scrollable container with max-height, auto height if few items -->
        <div id="activity-container" class="overflow-auto" style="max-height: 300px; transition: max-height 0.3s ease;">
            @foreach($recent_activities as $activity)
            <div class="activity-item mb-2 d-flex justify-content-between align-items-start">
                <div class="activity-dot me-2 
                        @if(in_array($activity->action, ['item created', 'added stock', 'added unit'])) bg-success
                        @elseif(in_array($activity->action, ['distributed', 'issued', 'installation'])) bg-primary
                        @elseif(in_array($activity->action, ['returned'])) bg-info
                        @elseif(in_array($activity->action, ['maintenance', 'borrowed', 'inspection'])) bg-warning
                        @elseif(in_array($activity->action, ['service completed'])) bg-dark
                        @elseif(in_array($activity->action, ['deleted'])) bg-danger
                        @else bg-secondary
                        @endif"
                    style="width:10px; height:10px; border-radius:50%; margin-top:6px;"></div>

                <div class="flex-grow-1">
                    <div class="fw-semibold">{{ ucfirst($activity->action ?? '') }}</div>
                    <div class="text-muted small">{{ $activity->notes }}</div>
                </div>

                <div class="activity-time text-muted small ms-2">
                    {{ $activity->created_at->diffForHumans() }}
                </div>
            </div>
            @endforeach
        </div>

        <!-- Toggle button -->
        @if(count($recent_activities) > 5)
        <div class="position-relative small clickable fw-500" id="toggle-activity" style="cursor:pointer; color: rgb(43, 45, 87);">
            Show More
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('toggle-activity');
        const container = document.getElementById('activity-container');

        if (btn && container) {
            let expanded = false;
            const collapsedHeight = '300px'; // same as max-height above
            const expandedHeight = container.scrollHeight + 'px';

            btn.addEventListener('click', function() {
                if (!expanded) {
                    container.style.maxHeight = expandedHeight;
                    btn.textContent = 'Show Less';
                    expanded = true;
                } else {
                    container.style.maxHeight = collapsedHeight;
                    btn.textContent = 'Show More';
                    expanded = false;
                }
            });
        }
    });
</script>

@endsection