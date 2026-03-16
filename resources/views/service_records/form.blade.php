<form action="{{ isset($service_record) ? route('service_records.update', $service_record->id) : route('service_records.store') }}"
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
        <!-- Hidden input to pass current page segment -->
        <input type="hidden" name="page" id="currentPageInput">

        <script>
            function setCurrentSegment() {
                const pageInput = document.getElementById('currentPageInput');
                if (pageInput) {
                    const segments = window.location.pathname.replace(/^\/|\/$/g, '').split('/');
                    const firstSegment = segments[0] || 'inventory'; // fallback if empty
                    pageInput.value = firstSegment;
                }
            }

            // Run immediately on page load
            setCurrentSegment();

            // If form is inside a Bootstrap modal, update on modal open
            const modal = document.getElementById('myFormModal');
            if (modal) {
                modal.addEventListener('show.bs.modal', setCurrentSegment);
            }
        </script>

        <!-- Item Name & Available Stock -->
        <div class="mb-3">
            <label class="form-label fw-bold">Item</label>
            <input type="text" class="form-control text-muted"
                value="{{ $selectedItem->name ?? '' }}" readonly>
            <small class="text-muted">Available: {{ $selectedItem->remaining ?? 0 }}</small>
            <input type="hidden" name="item_id" value="{{ $selectedItem->id ?? '' }}">
            <input type="hidden" name="page" value="{{ $page ?? 'inventory' }}">
        </div>

        <!-- Units Table (for non-consumables) -->
        <div class="mb-3" id="unitsSection">
            <label class="form-label fw-bold">Select Units</label>
            <div class="border rounded shadow-sm" style="max-height: 200px; overflow-y:auto; background-color:#f9f9f9;">
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
                    <tbody>
                        @if(isset($selectedItem) && $selectedItem->inventories)
                        @php
                        $counter = 1;

                        // Filter inventories
                        $availableInventories = $selectedItem->inventories->filter(function($inv) {
                        // Exclude if inventory has service records that are scheduled or in progress
                        $inService = $inv->serviceRecords
                        ->whereIn('status', ['scheduled', 'in progress'])
                        ->count() > 0;

                        // Exclude if inventory has a distribution type of distributed or issued
                        $inDistribution = $inv->itemDistributions
                        ->whereIn('type', ['distributed', 'issued'])
                        ->count() > 0;

                        // Exclude if inventory status is borrowed
                        $isBorrowed = strtolower($inv->status ?? '') === 'borrowed';

                        // Only include inventories that pass all checks
                        return !$inService && !$inDistribution && !$isBorrowed;
                        });
                        @endphp

                        @forelse($availableInventories as $inventory)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>{{ $selectedItem->name }}</td>
                            <td>{{ $inventory->qrCode->code ?? 'N/A' }}</td>
                            <td>
                                <input type="checkbox" class="unitCheckbox" name="inventory_ids[]" value="{{ $inventory->id }}">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">No available units</td>
                        </tr>
                        @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <!-- Service Type -->
            <div class="col-md-6 mb-3">
                <label for="service-record-type" class="form-label">Service Type</label>
                <select class="form-select" id="service-record-type" name="type" required>
                    <option value="">-- Select type --</option>
                    <option value="maintenance" {{ (isset($service_record) && $service_record->type == 'maintenance') ? 'selected' : '' }}>Maintenance</option>
                    <option value="installation" {{ (isset($service_record) && $service_record->type == 'installation') ? 'selected' : '' }}>Installation</option>
                    <option value="inspection" {{ (isset($service_record) && $service_record->type == 'inspection') ? 'selected' : '' }}>Inspection</option>
                </select>
            </div>

            <!-- Technician -->
            <div class="col-md-6 mb-3">
                <label for="technician" class="form-label">Technician</label>
                <input type="text" class="form-control" id="technician" name="technician"
                    value="{{ old('technician', $service_record->technician ?? '') }}" required>
            </div>
        </div>

        <!-- Service Date -->
        <div class="mb-3">
            <label for="service_date" class="form-label">Schedule Date</label>
            <input type="date" class="form-control" id="service_date" name="service_date"
                value="{{ old('service_date', isset($service_record) ? $service_record->service_date->format('Y-m-d') : date('Y-m-d')) }}" required>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="1"
                required>{{ old('description', $service_record->description ?? '') }}</textarea>
        </div>

        <!-- Picture Upload -->
        <div class="mb-3">
            <label class="form-label">Service Picture</label>
            <div class="border rounded p-3 text-center" id="service_record-dropzone" style="cursor:pointer; min-height:150px; display:flex; align-items:center; justify-content:center;">
                <input type="file" id="service_record-picture" name="picture" accept="image/*" style="display:none;">
                <img id="picture-preview"
                    src="{{ isset($service_record) && $service_record->picture ? asset('storage/' . $service_record->picture) : '' }}"
                    class="img-fluid rounded"
                    style="max-height:120px; {{ isset($service_record) && $service_record->picture ? '' : 'display:none;' }}">
                <div id="picture-placeholder" class="text-muted" style="{{ isset($service_record) && $service_record->picture ? 'display:none;' : '' }}">
                    Click or drag to upload picture
                </div>
            </div>
        </div>

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Save Service Record</button>
    </div>
</form>

<script>
    $(document).ready(function() {

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

        // Auto-fill modal when clicking an item
        $(document).on('click', '.add-service', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            const itemId = $(this).data('item-id');

            $.get(url, {
                item_id: itemId
            }, function(response) {
                $('#inventories_modal .modal-content').html(response);
                $('#inventories_modal').modal('show');
            });
        });

    });
</script>
<script>
    // Select / Deselect all units
    $(document).on('change', '#selectAllUnits', function() {
        $('.unitCheckbox').prop('checked', $(this).prop('checked'));
    });

    // Update Select All checkbox when individual checkbox changes
    $(document).on('change', '.unitCheckbox', function() {
        const total = $('.unitCheckbox').length;
        const checked = $('.unitCheckbox:checked').length;

        $('#selectAllUnits').prop('checked', total === checked);
    });
</script>