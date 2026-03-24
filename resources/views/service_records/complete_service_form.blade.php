    <form action="{{ route('service_records.complete_service', $service_record->id) }}"
        method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('POST') <!-- or leave POST -->

        <div class="modal-header" style="background-color: rgb(43, 45, 87);">
            <h5 class="modal-title text-white">
                Complete Service
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <!-- Hidden input for current page segment -->
            <input type="hidden" name="page" id="currentPageInput" value="{{ request()->segment(1) ?? 'inventory' }}">

            <div class="row">
                <!-- Item Name -->
                <div class="col-md-6 mb-3">
                    <label class="form-label bold-label">Item Name</label>
                    <input type="text"
                        class="form-control"
                        value="{{ $service_record->inventory->item->name ?? '' }}"
                        readonly>
                </div>

                <!-- QR Code -->
                <div class="col-md-6 mb-3">
                    <label class="form-label bold-label">QR Code</label>
                    <input type="text"
                        class="form-control"
                        value="{{ $service_record->inventory->qrCode->code ?? 'N/A' }}"
                        readonly>
                </div>

                <!-- Service Date -->
                <div class="col-md-6 mb-3">
                    <label for="service_date" class="form-label bold-label">Schedule Date</label>
                    <input type="text" class="form-control" id="service_date" name="service_date"
                        value="{{ old('service_date', isset($service_record) ? \Carbon\Carbon::parse($service_record->service_date)->format('M j, Y') : \Carbon\Carbon::now()->format('M j, Y')) }}"
                        readonly>
                </div>

                <!-- Completed Service Date -->
                <div class="col-md-6 mb-3">
                    <label for="completed_date" class="form-label required">Completed Date</label>
                    <input type="date" class="form-control" id="completed_date" name="completed_date"
                        value="{{ old('completed_date', isset($service_record) ? \Carbon\Carbon::parse($service_record->service_date)->format('Y-m-d') : date('Y-m-d')) }}"
                        min="{{ isset($service_record) ? \Carbon\Carbon::parse($service_record->service_date)->format('Y-m-d') : date('Y-m-d') }}"
                        required>
                </div>

                <!-- Remarks -->
                <div class="mb-3">
                    <label for="remarks" class="form-label bold-label">
                        Remarks
                    </label>
                    <textarea class="form-control"
                        id="remarks" name="remarks" rows="1" placeholder="Service result or technician remarks"></textarea>
                </div>

                <!-- Picture Upload -->
                <div class="mb-3">
                    <label class="form-label bold-label">
                        Completion Picture (Optional)
                    </label>

                    <input type="file"
                        class="form-control"
                        name="picture"
                        accept="image/*">
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Complete Service</button>
        </div>
    </form>