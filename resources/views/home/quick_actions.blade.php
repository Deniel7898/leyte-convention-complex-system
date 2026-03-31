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
                            <div class="action-title text-success">Distribute / Return</div>
                            <div class="action-desc">Scan item to distribute or return</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="quick-action-box warning" data-action="service" style="cursor:pointer;">
                        <div class="icon text-warning"><i class="bi bi-tools"></i></div>
                        <div>
                            <div class="action-title text-warning">Service / Complete</div>
                            <div class="action-desc">Scan item for service or complete</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scan Modal -->
            <div class="modal fade" id="scanModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 shadow-lg">
                        <div class="modal-header border-0" style="background-color: rgb(43, 45, 87);">
                            <h5 class="modal-title bold-label text-white" id="scanModalTitle"></h5>
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
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label bold-label">Item</label>
                                        <input type="text" class="form-control text-muted" id="distributionItemName"
                                            readonly>
                                        <small class="text-muted" id="distributionItemRemaining"
                                            style="display:none;">Available: 0</small>
                                        <small class="text-muted" id="distributionScannedQR"
                                            style="display:none;"></small>
                                    </div>

                                    <!-- Type -->
                                    <div class="col-md-6 mb-1" id="distributionTypeWrapper" style="display:none;">
                                        <label class="form-label required">Type</label>
                                        <select class="form-select" name="type" id="distributionType" required>
                                            <option value="" hidden>Select Type</option>
                                            <option value="distributed" style="display:none;">Distributed</option>
                                            <option value="borrowed">Borrowed</option>
                                            <option value="issued">Issued</option>
                                        </select>
                                    </div>

                                    <!-- Quantity (consumable) -->
                                    <div class="col-md-6 mb-3" id="distributionQuantityWrapper" style="display:none;">
                                        <label class="form-label required">Quantity</label>
                                        <input type="number" class="form-control" name="quantity"
                                            id="distributionQuantity" min="1" value="">
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
                                        <textarea class="form-control" name="notes" rows="1 "
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

            <!-- Return Modal -->
            <div class="modal fade" id="returnModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content rounded-4 shadow-lg">
                        <form id="returnForm" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="modal-header" style="background-color: rgb(43,45,87);">
                                <h5 class="modal-title text-white">Return Item</h5>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                    <input type="hidden" name="distribution_id" id="returnDistributionId">
                                    <input type="hidden" name="page" value="home">

                                    <!-- Item -->
                                    <div class="col-md-6 mb-1">
                                        <label class="bold-label">Item Name</label>
                                        <input type="text" id="returnItemName" class="form-control" readonly>
                                        <small class="text-muted" id="returnQR"></small>
                                    </div>

                                    <!-- Borrower -->
                                    <div class="col-md-6 mb-1">
                                        <label class="bold-label">Borrower</label>
                                        <input type="text" id="returnBorrower" class="form-control" readonly>
                                    </div>

                                    <!-- Borrowed Date -->
                                    <div class="col-md-6 mb-3">
                                        <label class="bold-label">Borrowed Date</label>
                                        <input type="text" id="returnDateBorrowed" class="form-control" readonly>
                                    </div>

                                    <!-- Returned Date -->
                                    <div class="col-md-6 mb-3">
                                        <label class="required">Returned Date</label>
                                        <input type="date" name="returned_date" id="returnDate" class="form-control"
                                            required>
                                    </div>

                                    <!-- Notes -->
                                    <div class="mb-3">
                                        <label class="bold-label">Notes</label>
                                        <textarea name="notes" id="returnNotes" class="form-control" rows="1"
                                            placeholder="Condition of the item upon return"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn text-white" style="background-color: rgb(43,45,87);">
                                    Confirm Return
                                </button>
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
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label bold-label">Item Name</label>
                                        <input type="text" class="form-control text-muted" id="serviceItemName"
                                            readonly>
                                        <small class="text-muted" id="serviceScannedQR"></small>
                                    </div>

                                    <!-- Service Type -->
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label required">Service Type</label>
                                        <select class="form-select" id="serviceType" name="type" required>
                                            <option value="" hidden>Select type</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="installation">Installation</option>
                                            <option value="inspection">Inspection</option>
                                        </select>
                                    </div>

                                    <!-- Hidden input for inventory ID -->
                                    <input type="hidden" name="inventory_ids[]" id="serviceInventoryId" value="">

                                    <!-- Service Date -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Schedule Date</label>
                                        <input type="date" class="form-control" id="serviceDate" name="service_date"
                                            value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                                    </div>

                                    <!-- Technician -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Incharge / Technician</label>
                                        <input type="text" class="form-control" id="technician" name="technician">
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-3">
                                        <label class="form-label bold-label">Service Description</label>
                                        <textarea class="form-control" id="serviceDescription" name="description"
                                            rows="1"></textarea>
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

            <!-- Complete Service Modal -->
            <div class="modal fade" id="completeServiceModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content rounded-4 shadow-lg">
                        <form id="completeServiceForm" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="modal-header" style="background-color: rgb(43, 45, 87);">
                                <h5 class="modal-title text-white">Complete Service</h5>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <input type="hidden" name="service_record_id" id="completeServiceId">
                                <input type="hidden" name="page" value="home">

                                <div class="row">
                                    <!-- Item Name -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Item Name</label>
                                        <input type="text" class="form-control" id="completeItemName" readonly>
                                    </div>

                                    <!-- QR -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">QR Code</label>
                                        <input type="text" class="form-control" id="completeQR" readonly>
                                    </div>

                                    <!-- Schedule Date -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label bold-label">Schedule Date</label>
                                        <input type="date" class="form-control" id="scheduleDate" name="schedule_date"
                                            readonly>
                                    </div>

                                    <!-- Completed Date -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Completed Date</label>
                                        <input type="date" class="form-control" id="completedDate" name="completed_date"
                                            required>
                                    </div>

                                    <!-- Remarks -->
                                    <div class="mb-3">
                                        <label class="form-label bold-label">Remarks</label>
                                        <textarea class="form-control" name="remarks" id="completeRemarks" rows="1"
                                            placeholder="Service result or technician remarks"></textarea>
                                    </div>

                                    <!-- Picture -->
                                    <div class="mb-3">
                                        <label class="form-label">Completion Picture</label>
                                        <input type="file" class="form-control" name="picture" accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">
                                    Complete Service
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>