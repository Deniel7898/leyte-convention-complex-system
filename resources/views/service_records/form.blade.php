<form action="{{ isset($service_record) ? route('service_records.update', $service_record->id) : route('service_records.store') }}" method="POST">
    @csrf
    @if(isset($service_record))
    @method('PUT')
    @endif

    <div class="modal-header" style="background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($service_record) ? 'Edit' : 'New' }} Item Service</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <!-- Item Selection (non-consumable) -->
        @if(!isset($service_record))
        <div class="mb-4">
            <label class="form-label fw-bold">Select Items for Service</label>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="border rounded shadow-sm" style="max-height: 250px; overflow-y: auto; background-color: #f9f9f9;">
                        <table class="table table-sm mb-0 align-middle text-center table-hover" id="unitsTable">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width:50px;">
                                        <input type="checkbox" id="select-all">
                                    </th>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>QR Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($nonConsumables->count() > 0)
                                @foreach($nonConsumables as $nonConsumable)
                                <tr>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="inventory_non_consumable_ids[]"
                                            value="{{ $nonConsumable->id }}"
                                            class="item-checkbox">
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $nonConsumable->item->name }}</td>
                                    <td>{{ $nonConsumable->qrCode->code ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="4" class="text-center text-muted text-danger">
                                        {{ __('No non-consumable items found.') }}
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="mb-4">
            <label class="form-label fw-bold">Selected Item</label>

            <input type="text" class="form-control"
                value="{{ $service_record->inventoryNonConsumable->item->name ?? 'N/A' }}"
                readonly>

            <!-- IMPORTANT: send the ID -->
            <input type="hidden"
                name="inventory_non_consumable_ids[]"
                value="{{ $service_record->inventory_non_consumable_id }}">
        </div>
        @endif

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="service_record-type" class="form-label">Service Type</label>
                <select class="form-select" id="service_record-type" name="type" required>
                    <option value="">Select type</option>
                    <option value="0" {{ (isset($service_record) && $service_record->type == 0) || old('type') === '0' ? 'selected' : '' }}>Maintenance</option>
                    <option value="1" {{ (isset($service_record) && $service_record->type == 1) || old('type') === '1' ? 'selected' : '' }}>Installation</option>
                </select>
            </div>

            <!-- Schedule Date -->
            <div class="col-md-6 mb-3">
                <label for="schedule_date" class="form-label">Schedule Date</label>
                <input type="date" class="form-control" id="schedule_date" name="schedule_date"
                    value="{{ old('schedule_date', isset($service_record) ? \Carbon\Carbon::parse($service_record->schedule_date)->format('Y-m-d') : '') }}" required>
            </div>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $service_record->description ?? '') }}</textarea>
        </div>

        <!-- Person in Charge -->
        <div class="mb-3">
            <label for="encharge_person" class="form-label">Person in Charge</label>
            <input type="text" class="form-control" id="encharge_person" name="encharge_person"
                value="{{ old('incharge_person', $service_record->encharge_person ?? '') }}" required>
        </div>

        <!-- Simple Picture Upload -->
        <div class="mb-3">
            <label class="form-label">Item Picture</label>
            <div class="border rounded p-3 text-center"
                id="service_record-dropzone"
                style="cursor: pointer; min-height: 150px; display: flex; align-items: center; justify-content: center;">

                <!-- Fixed ID to match JS -->
                <input type="file"
                    id="service_record-picture"
                    name="picture"
                    accept="image/*"
                    onchange="previewPicture(event)"
                    style="display:none;">

                <img id="picture-preview"
                    src="{{ isset($service_record) && $service_record->picture ? asset('storage/' . $service_record->picture) : '' }}"
                    class="img-fluid rounded"
                    style="max-height: 120px; {{ isset($service_record) && $service_record->picture ? '' : 'display:none;' }}">

                <div id="picture-placeholder"
                    class="text-muted"
                    style="{{ isset($service_record) && $service_record->picture ? 'display:none;' : '' }}">
                    Click or drag to upload picture
                </div>
            </div>
        </div>

        <!-- Hidden input for item ID -->
        <input type="hidden" name="item_id" value="{{ $selectedItem->id ?? ($service_record->id ?? '') }}">
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Save Item Service</button>
    </div>
</form>


<script>
    (function() {
        const dropzone = document.getElementById('service_record-dropzone');
        const inputFile = document.getElementById('service_record-picture'); // corrected ID
        const preview = document.getElementById('picture-preview');
        const placeholder = document.getElementById('picture-placeholder');

        if (!dropzone || !inputFile) return;

        // Click opens file dialog
        dropzone.addEventListener('click', () => inputFile.click());

        // Drag & Drop
        dropzone.addEventListener('dragover', e => e.preventDefault());
        dropzone.addEventListener('drop', e => {
            e.preventDefault();
            if (e.dataTransfer.files.length > 0) {
                inputFile.files = e.dataTransfer.files;
                previewFile(e.dataTransfer.files[0]);
            }
        });

        // Preview function
        function previewFile(file) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }

        // Input change event
        inputFile.addEventListener('change', () => {
            if (inputFile.files.length > 0) {
                previewFile(inputFile.files[0]);
            }
        });
    })();
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const selectAll = document.getElementById('select-all');

        if (selectAll) {

            const checkboxes = document.querySelectorAll('.item-checkbox');

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                    toggleRowHighlight(checkbox);
                });
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    toggleRowHighlight(this);
                });
            });

            function toggleRowHighlight(checkbox) {
                if (checkbox.checked) {
                    checkbox.closest('tr').classList.add('table-primary');
                } else {
                    checkbox.closest('tr').classList.remove('table-primary');
                }
            }
        }
    });
</script>