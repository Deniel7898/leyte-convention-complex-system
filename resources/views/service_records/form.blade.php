<form
    action="{{ isset($service_record) ? route('service_records.update', $service_record->id) : route('service_records.store') }}"
    method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($service_record))
        @method('PUT')
    @endif

    <div class="modal-header" style="background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($service_record) ? 'Edit' : 'New' }} Item Service</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <!-- Hidden input for current page segment -->
        <input type="hidden" name="page" id="currentPageInput" value="{{ request()->segment(1) ?? 'inventory' }}">

        <!-- Hidden input for service ID -->
        <input type="hidden" name="service_id" value="{{ $service_record->id ?? '' }}">

        <div class="row">
            <!-- Item Name & Available Stock -->
            <div class="col-md-6 mb-1">
                <label class="form-label bold-label">Item Name</label>
                <input type="text" class="form-control text-muted" value="{{ $selectedItem->name ?? '' }}" readonly>
                <input type="hidden" name="item_id" value="{{ $selectedItem->id ?? '' }}">
                <small class="text-muted">
                    @if($selectedQr)
                        QR Code: {{ $selectedQr }}
                    @else
                        Available: {{ $selectedItem->remaining ?? 0 }}
                    @endif
                </small>
            </div>

            <!-- Service Type -->
            <div class="col-md-6 mb-1">
                <label for="service-record-type" class="form-label required">Service Type</label>
                <select class="form-select" id="service-record-type" name="type" required>
                    <option value="" hidden>Select Type
                    </option>
                    <option value="maintenance" {{ (isset($service_record) && $service_record->type == 'maintenance') ? 'selected' : '' }}>Maintenance</option>
                    <option value="installation" {{ (isset($service_record) && $service_record->type == 'installation') ? 'selected' : '' }}>Installation</option>
                    <option value="inspection" {{ (isset($service_record) && $service_record->type == 'inspection') ? 'selected' : '' }}>Inspection</option>
                </select>
            </div>


            <!-- Units Table (for non-consumables) -->

            @if(isset($selectedItem) && $selectedItem->inventories)
                @php
                    // Filter inventories
                    $availableInventories = $selectedItem->inventories->filter(function ($inv) {
                        // Exclude if inventory has service records that are scheduled or in progress
                        $inService = $inv->serviceRecords
                            ->whereIn('status', ['scheduled', 'in progress'])
                            ->count() > 0;

                        // Exclude if inventory has a distribution type of distributed or issued
                        $inDistribution = $inv->itemDistributions
                            ->whereIn('type', ['distributed'])
                            ->count() > 0;

                        // Check if status is borrowed or issued
                        $status = strtolower($inv->status ?? '');
                        $isBorrowedOrIssued = in_array($status, ['borrowed', 'issued']);

                        // Only include inventories that pass all checks
                        return !$inService && !$inDistribution && !$isBorrowedOrIssued;
                    });

                    // Show units table if there is at least one inventory and quickAction is not active
                    $showUnitsTable = $availableInventories->count() > 0 && empty($quickAction);
                @endphp

                @if(!isset($service_record) && $showUnitsTable)
                    <div class="mb-3" id="unitsSection">
                        <label class="form-label required">Select Units</label>
                        <div class="border rounded shadow-sm"
                            style="max-height: 200px; overflow-y:auto; background-color:#f9f9f9;">
                            <table class="table table-sm mb-0 text-center align-middle">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>#</th>
                                        <th>Item Name</th>
                                        <th>QR Code</th>
                                        <th>
                                            <input type="checkbox" id="selectAllUnits" title="Select/Deselect All">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $counter = 1; @endphp
                                    @forelse($availableInventories as $inventory)
                                        <tr>
                                            <td>{{ $counter++ }}</td>
                                            <td>{{ $selectedItem->name }}</td>
                                            <td>{{ $inventory->qrCode->code ?? 'N/A' }}</td>
                                            <td>
                                                @if(isset($selectedInventory) && $inventory->id == $selectedInventory)
                                                    <input type="hidden" name="inventory_ids[]" value="{{ $inventory->id }}">
                                                    <span class="text-success">Auto-selected</span>
                                                @else
                                                    <input type="checkbox" class="unitCheckbox" name="inventory_ids[]"
                                                        value="{{ $inventory->id }}">
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">
                                                @if(isset($selectedInventory))
                                                    <input type="hidden" name="inventory_ids[]" value="{{ $selectedInventory }}">
                                                    Auto-selected inventory
                                                @else
                                                    No available units
                                                @endif
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif(isset($selectedInventory))
                    {{-- Case when no available inventories, but a specific inventory was clicked --}}
                    <input type="hidden" name="inventory_ids[]" value="{{ $selectedInventory }}">
                @endif
            @endif

            <!-- Service Date -->
            <div class="col-md-6 mb-3">
                <label for="service_date" class="form-label required">Schedule Date</label>
                <input type="date" class="form-control" id="service_date" name="service_date"
                    value="{{ old('service_date', isset($service_record) ? \Carbon\Carbon::parse($service_record->service_date)->format('Y-m-d') : date('Y-m-d')) }}"
                    required min="{{ date('Y-m-d') }}">
            </div>

            <!-- Technician -->
            <div class="col-md-6 mb-3">
                <label for="technician" class="form-label bold-label">Incharge / Technician</label>
                <input type="text" class="form-control" id="technician" name="technician"
                    value="{{ old('technician', $service_record->technician ?? '') }}">
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label bold-label"> Service Description</label>
                <textarea class="form-control" id="description" name="description"
                    rows="1">{{ old('description', $service_record->description ?? '') }}</textarea>
            </div>

            <!-- Picture Upload -->
            <div class="mb-3">
                <label class="form-label bold-label">Service Picture</label>
                <div class="border rounded p-3 text-center" id="service_record-dropzone"
                    style="cursor:pointer; min-height:150px; display:flex; align-items:center; justify-content:center;">
                    <input type="file" id="service_record-picture" name="picture" accept="image/*"
                        style="display:none;">
                    <img id="picture-preview"
                        src="{{ isset($service_record) && $service_record->picture ? asset('storage/' . $service_record->picture) : '' }}"
                        class="img-fluid rounded"
                        style="max-height:120px; {{ isset($service_record) && $service_record->picture ? '' : 'display:none;' }}">
                    <div id="picture-placeholder" class="text-muted"
                        style="{{ isset($service_record) && $service_record->picture ? 'display:none;' : '' }}">
                        Click or drag to upload picture
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Save Service
            Record</button>
    </div>
</form>

<script>
    $(document).ready(function () {

        // Drag & Drop preview
        const dropzone = document.getElementById('service_record-dropzone');
        const inputFile = document.getElementById('service_record-picture');
        const preview = document.getElementById('picture-preview');
        const placeholder = document.getElementById('picture-placeholder');

        if (dropzone && inputFile) {
            dropzone.addEventListener('click', () => inputFile.click());
            dropzone.addEventListener('dragover', e => e.preventDefault());
            dropzone.addEventListener('drop', e => {
                e.preventDefault();
                if (e.dataTransfer.files.length > 0) {
                    inputFile.files = e.dataTransfer.files;
                    previewFile(e.dataTransfer.files[0]);
                }
            });

            inputFile.addEventListener('change', () => {
                if (inputFile.files.length > 0) {
                    previewFile(inputFile.files[0]);
                }
            });

            function previewFile(file) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        }

    });
</script>
<script>
    // Select / Deselect all units
    $(document).on('change', '#selectAllUnits', function () {
        $('.unitCheckbox').prop('checked', $(this).prop('checked'));
    });

    // Update Select All checkbox when individual checkbox changes
    $(document).on('change', '.unitCheckbox', function () {
        const total = $('.unitCheckbox').length;
        const checked = $('.unitCheckbox:checked').length;

        $('#selectAllUnits').prop('checked', total === checked);
    });
</script>