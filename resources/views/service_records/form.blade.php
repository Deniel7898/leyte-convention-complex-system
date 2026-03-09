<form action="{{ isset($service_record) ? route('service_records.update', $service_record->id) : route('service_records.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($service_record))
    @method('PUT')
    @endif

    <div class="modal-header" style="background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($service_record) ? 'Edit' : 'New' }} Item Service</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">

        {{-- Category Selection --}}
        <div class="mb-3 col-md-6">
            <label class="form-label fw-bold">Select Category</label>
            <select id="categorySelect" class="form-select">
                <option value="">-- All Categories --</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <small class="text-muted">Selecting a category will filter the inventory items below.</small>
        </div>

        {{-- Item Selection for New Records --}}
        @if(!isset($service_record))
        <div class="mb-4">
            <label class="form-label fw-bold">Select Items for Service</label>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="border rounded shadow-sm" style="max-height: 250px; overflow-y: auto; background-color: #f9f9f9;">
                        <table class="table table-sm mb-0 align-middle text-center table-hover" id="inventoryTable">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width:50px;"><input type="checkbox" id="select-all"></th>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>QR Code</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTableBody">
                                @foreach($inventory as $item)
                                <tr data-category="{{ $item->category_id }}">
                                    <td><input type="checkbox" name="inventory_ids[]" value="{{ $item->id }}" class="item-checkbox"></td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->item->name ?? 'N/A' }}</td>
                                    <td>{{ $item->qrCode->code ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @else
        {{-- Single item for editing --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Selected Item</label>
            <input type="text" class="form-control" value="{{ $service_record->inventory->name ?? 'N/A' }}" readonly>
            <input type="hidden" name="inventory_ids[]" value="{{ $service_record->inventory_id }}">
        </div>
        @endif

        {{-- Service Type and Schedule Date --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="service_record-type" class="form-label">Service Type</label>
                <select class="form-select" id="service_record-type" name="type" required>
                    <option value="">Select type</option>
                    <option value="0" {{ (isset($service_record) && $service_record->type == 0) || old('type') === '0' ? 'selected' : '' }}>Maintenance</option>
                    <option value="1" {{ (isset($service_record) && $service_record->type == 1) || old('type') === '1' ? 'selected' : '' }}>Installation</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="schedule_date" class="form-label">Schedule Date</label>
                <input type="date" class="form-control" id="schedule_date" name="schedule_date"
                    value="{{ old('schedule_date', isset($service_record) ? \Carbon\Carbon::parse($service_record->schedule_date)->format('Y-m-d') : '') }}" required>
            </div>
        </div>

        {{-- Person in Charge and Completed Date --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="encharge_person" class="form-label">Person in Charge</label>
                <input type="text" class="form-control" id="encharge_person" name="encharge_person"
                    value="{{ old('encharge_person', $service_record->encharge_person ?? '') }}" required>
            </div>

            @if(isset($service_record) && $service_record->completed_date)
            <div class="col-md-6 mb-3">
                <label for="completed_date" class="form-label">Completed Date</label>
                <input type="date" class="form-control" id="completed_date" name="completed_date"
                    value="{{ old('completed_date', \Carbon\Carbon::parse($service_record->completed_date)->format('Y-m-d')) }}">
            </div>
            @endif
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $service_record->description ?? '') }}</textarea>
        </div>

        {{-- Picture Upload --}}
        <div class="mb-3">
            <label class="form-label">Item Service Picture</label>
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
        <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Save Item Service</button>
    </div>
</form>

{{-- JS for Drag & Drop Preview --}}
<script>
    (function() {
        const dropzone = document.getElementById('service_record-dropzone');
        const inputFile = document.getElementById('service_record-picture');
        const preview = document.getElementById('picture-preview');
        const placeholder = document.getElementById('picture-placeholder');

        if (!dropzone || !inputFile) return;

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
    })();
</script>

{{-- JS for Select All checkboxes & row highlight --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selectAll = document.getElementById('select-all');
        if (!selectAll) return;

        const checkboxes = document.querySelectorAll('.item-checkbox');
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                if ($(cb).closest('tr').is(':visible')) { // only visible rows
                    cb.checked = this.checked;
                    toggleRowHighlight(cb);
                }
            });
        });

        checkboxes.forEach(cb => cb.addEventListener('change', function() {
            toggleRowHighlight(this);
        }));

        function toggleRowHighlight(checkbox) {
            if (checkbox.checked) {
                checkbox.closest('tr').classList.add('table-primary');
            } else {
                checkbox.closest('tr').classList.remove('table-primary');
            }
        }
    });
</script>

{{-- JS for Category Filter --}}
<script>
    $(document).ready(function() {
        const allRows = $('#inventoryTableBody tr').clone(); // store original rows

        $('#categorySelect').on('change', function() {
            const selectedCategory = $(this).val();
            const tbody = $('#inventoryTableBody');

            tbody.empty(); // clear table body

            allRows.each(function() {
                const rowCategory = $(this).data('category').toString();
                if (!selectedCategory || rowCategory === selectedCategory) {
                    tbody.append(this);
                }
            });

            // Reset checkboxes and highlights
            $('.item-checkbox').prop('checked', false);
            $('tr').removeClass('table-primary');
            $('#select-all').prop('checked', false);
        });
    });
</script>