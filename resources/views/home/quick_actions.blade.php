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
                        <div class="modal-header border-0" style="background-color: rgb(43, 45, 87);">
                            <h5 class="modal-title fw-bold text-white" id="scanModalTitle">Add Stock</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">

                            <div class="qr-scanner-container">
                                <!-- QR ICON -->
                                <i class="bi bi-qr-code-scan qr-icon"></i>

                                <!-- SCAN LINE -->
                                <div class="scan-line"></div>
                            </div>

                            <style>
                                .qr-scanner-container {
                                    position: relative;
                                    width: 120px;
                                    height: 120px;
                                    margin: 0 auto;
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                }

                                /* ICON */
                                .qr-icon {
                                    font-size: 100px;
                                    color: #9ca3af;
                                    /* soft grey */
                                    z-index: 2;
                                    animation: heartbeat 5.5s ease-in-out infinite;
                                    position: relative;
                                }

                                /* GLOW EFFECT (soft white/grey) */
                                .qr-icon::after {
                                    content: "";
                                    position: absolute;
                                    top: 50%;
                                    left: 50%;
                                    width: 90px;
                                    height: 90px;
                                    background: rgba(255, 255, 255, 0.10);
                                    /* light glow instead of blue */
                                    transform: translate(-50%, -50%);
                                    border-radius: 20px;
                                    filter: blur(18px);
                                    z-index: -1;
                                    animation: glow 4s ease-in-out infinite;
                                }

                                /* SCAN LINE (LIGHT SWEEP EFFECT) */
                                .scan-line {
                                    position: absolute;
                                    width: 85%;
                                    height: 6px;
                                    background: linear-gradient(90deg,
                                            transparent,
                                            rgba(255, 255, 255, 0.9),
                                            transparent);
                                    border-radius: 4px;
                                    filter: blur(1px);
                                    /* makes it look like light */
                                    animation: scan 2.5s ease-in-out infinite;
                                    z-index: 3;
                                }

                                /* SCAN ANIMATION (smooth light pass) */
                                @keyframes scan {
                                    0% {
                                        top: 10%;
                                        opacity: 0;
                                    }

                                    15% {
                                        opacity: 1;
                                    }

                                    50% {
                                        top: 50%;
                                        opacity: 1;
                                    }

                                    85% {
                                        opacity: 1;
                                    }

                                    100% {
                                        top: 90%;
                                        opacity: 0;
                                    }
                                }

                                /* HEARTBEAT */
                                @keyframes heartbeat {

                                    0%,
                                    100% {
                                        transform: scale(1);
                                    }

                                    25% {
                                        transform: scale(1.15);
                                    }

                                    50% {
                                        transform: scale(1);
                                    }

                                    75% {
                                        transform: scale(1.1);
                                    }
                                }

                                /* GLOW */
                                @keyframes glow {

                                    0%,
                                    100% {
                                        opacity: 0.4;
                                        transform: translate(-50%, -50%) scale(1);
                                    }

                                    50% {
                                        opacity: 0.8;
                                        transform: translate(-50%, -50%) scale(1.2);
                                    }
                                }
                            </style>

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
            <div class="modal fade" id="restockFormModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content rounded-4 shadow-lg">

                        <!-- Modal Header -->
                        <div class="modal-header" style="background-color: rgb(43, 45, 87);">
                            <h5 class="modal-title text-white" id="actionTitle">Restock Item</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body">
                            <form action="{{ route('inventory.add_stock') }}" method="POST"
                                enctype="multipart/form-data" id="restockItemForm">
                                @csrf

                                <input type="hidden" name="item_id" value="">
                                <input type="hidden" name="page" value="home">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Category</label>
                                        <input type="text" class="form-control text-muted" value="" readonly
                                            id="itemCategory">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Item</label>
                                        <input type="text" class="form-control text-muted" value="" readonly
                                            id="itemName">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Type</label>
                                        <input type="text" class="form-control text-muted" value="" readonly
                                            id="itemType">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Unit</label>
                                        <input type="text" class="form-control text-muted" value="" readonly
                                            id="itemUnit">
                                    </div>

                                    <div class="col-md-6 mb-3" id="quantityGroup">
                                        <label for="quantity" class="form-label required">Quantity</label>
                                        <input type="number" id="quantity" name="quantity" class="form-control" min="1"
                                            maxlength="3" pattern="\d{1,3}" placeholder="Enter Quantity (max-999)">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Supplier</label>
                                        <input type="text" class="form-control text-muted" value="" readonly
                                            id="itemSupplier">
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
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn text-white"
                                        style="background-color: rgb(43, 45, 87);" id="actionSubmitBtn">Submit</button>
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
                        <form action="{{ route('item_distributions.store') }}" method="POST"
                            enctype="multipart/form-data" id="itemDistributionForm">
                            @csrf

                            <div class="modal-header" style="background-color: rgb(43, 45, 87);">
                                <h5 class="modal-title text-white">New Distribution</h5>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body p-4 bg-white rounded">
                                <!-- Hidden Inputs -->
                                <input type="hidden" name="item_id" id="distributionItemId" value="">
                                <input type="hidden" name="page" value="home">

                                <div class="row">
                                    <!-- Item Name -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Item</label>
                                        <input type="text" class="form-control text-muted" id="distributionItemName"
                                            readonly>
                                        <small class="text-muted" id="distributionItemRemaining" style="display:none;">Available: 0</small>
                                        <small class="text-muted" id="distributionScannedQR" style="display:none;"></small>
                                    </div>

                                    <!-- Type -->
                                    <div class="col-md-6 mb-3" id="distributionTypeWrapper" style="display:none;">
                                        <label class="form-label required">Type</label>
                                        <select class="form-select" name="type" id="distributionType" required>
                                            <option value="">-- Select type --</option>
                                            <option value="distributed" style="display:none;">Distributed</option>
                                            <option value="borrowed">Borrowed</option>
                                            <option value="issued">Issued</option>
                                        </select>
                                    </div>

                                    <!-- Quantity (consumable) -->
                                    <div class="col-md-6 mb-3" id="distributionQuantityWrapper" style="display:none;">
                                        <label class="form-label required">Quantity</label>
                                        <input type="number" class="form-control" name="quantity"
                                            id="distributionQuantity" min="1" value="1">
                                    </div>

                                    <!-- Hidden input for inventory ID -->
                                    <input type="hidden" name="inventory_ids[]" id="distributionInventoryId" value="">

                                    <!-- Department/Borrower -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Department / Borrower</label>
                                        <input type="text" class="form-control" name="department_or_borrower"
                                            placeholder="Enter department or borrower" required>
                                    </div>

                                    <!-- Distribution Date -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Distribution Date</label>
                                        <input type="date" class="form-control" name="distribution_date"
                                            value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}">
                                    </div>

                                    <!-- Due Date -->
                                    <div class="mb-3" id="distributionDueDateWrapper" style="display:none;">
                                        <label class="form-label required">Due Date</label>
                                        <input type="date" class="form-control" name="due_date"
                                            min="{{ date('Y-m-d') }}">
                                    </div>

                                    <!-- Notes -->
                                    <div class="mb-3">
                                        <label class="form-label bold-label">Notes</label>
                                        <textarea class="form-control" name="notes" rows="2"
                                            placeholder="Optional notes"></textarea>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn text-white"
                                        style="background-color: rgb(43, 45, 87);">Save Distribution</button>
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
                        <form action="{{ route('service_records.store') }}" method="POST" enctype="multipart/form-data"
                            id="serviceRecordForm">
                            @csrf

                            <!-- Modal Header -->
                            <div class="modal-header" style="background-color: rgb(43, 45, 87);">
                                <h5 class="modal-title text-white">New Item Service</h5>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body">
                                <input type="hidden" name="item_id" id="serviceItemId" value="">
                                <input type="hidden" name="page" value="home">

                                <div class="row">
                                    <!-- Item Info -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Item</label>
                                        <input type="text" class="form-control text-muted" id="serviceItemName"
                                            readonly>
                                        <small class="text-muted" id="serviceScannedQR"></small>
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

                                    <!-- Hidden input for inventory ID -->
                                    <input type="hidden" name="inventory_ids[]" id="serviceInventoryId" value="">

                                    <!-- Technician -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Technician</label>
                                        <input type="text" class="form-control" id="technician" name="technician"
                                            required>
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
                                        <textarea class="form-control" id="serviceDescription" name="description"
                                            rows="2" required></textarea>
                                    </div>

                                    <!-- Picture Upload -->
                                    <div class="mb-3">
                                        <label class="form-label bold-label">Service Picture</label>
                                        <div class="border rounded p-3 text-center" id="serviceDropzone"
                                            style="cursor:pointer; min-height:150px; display:flex; align-items:center; justify-content:center;">
                                            <input type="file" id="servicePicture" name="picture" accept="image/*"
                                                style="display:none;">
                                            <img id="servicePicturePreview" class="img-fluid rounded"
                                                style="max-height:120px; display:none;">
                                            <div id="servicePicturePlaceholder" class="text-muted">Click or drag to
                                                upload picture</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn text-white"
                                    style="background-color: rgb(43, 45, 87);">Save Service Record</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>