<form action="{{ isset($item) ? route('items.update', $item->id) : route('items.store') }}"
    method="POST"
    enctype="multipart/form-data">

    @csrf
    @if(isset($item))
    @method('PUT')
    @endif

    <div class="modal-header" style=" background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($item) ? 'Edit Item' : 'Add Item' }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">

        <!-- Item Name -->
        <div class="mb-3">
            <label for="item-name" class="form-label">Item Name</label>
            <input type="text" class="form-control" id="item-name" name="name" value="{{ isset($item) ? $item->name : '' }}" required>
        </div>

        <div class="row">
            <!-- Type -->
            @if(!isset($item))
            <div class="col-md-6 mb-3">
                <label for="item-type" class="form-label">Type</label>
                <select class="form-select" id="item-type" name="type" required>
                    <option value="">Select type</option>
                    <option value="0" {{ (isset($item) && $item->type == 0) ? 'selected' : '' }}>Consumable</option>
                    <option value="1" {{ (isset($item) && $item->type == 1) ? 'selected' : '' }}>Non-Consumable</option>
                </select>
            </div>
            @else
            <input type="hidden" name="type" value="{{ $item->type }}">
            @endif

            <!-- Quantity -->
            @if(!isset($item))
            <div class="col-md-6 mb-3">
                <label for="item-quantity" class="form-label">Quantity</label>
                <input type="number"
                    class="form-control"
                    id="item-quantity"
                    name="quantity"
                    min="1"
                    required>
            </div>
            @else
            <input type="hidden" name="quantity" value="{{ $item->quantity }}">
            @endif
        </div>

        <div class="row">
            <!-- Unit -->
            <div class="col-md-6 mb-3">
                <label for="item-unit" class="form-label">Unit</label>
                <select class="form-select" id="item-unit" name="unit_id" required>
                    <option value="">Select Unit</option>
                    @foreach ($units as $unit)
                    <option value="{{ $unit->id }}"
                        {{ (isset($item) && $item->unit_id == $unit->id) ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Category -->
            <div class="col-md-6 mb-3">
                <label for="item-category" class="form-label">Category</label>
                <select class="form-select" id="item-category" name="category_id" required>
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ (isset($item) && $item->category_id == $category->id) ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <!-- Status -->
            <div class="col-md-6 mb-3">
                <label for="item-status" class="form-label">Status</label>
                <select class="form-select" id="item-status" name="status" required>
                    <option value="">Select Status</option>
                    <option value="1" {{ (isset($item) && $item->status == 1) ? 'selected' : '' }}>Available</option>
                    <option value="0" {{ (isset($item) && $item->status == 0) ? 'selected' : '' }}>Not Available</option>
                </select>
            </div>

            <!-- Received Date -->
            <div class="col-md-6 mb-3">
                <label for="received-date" class="form-label">Received Date</label>
                <input type="date" class="form-control" id="received-date" name="received_date"
                    value="{{ isset($inventory) ? $inventory->received_date : date('Y-m-d') }}">
            </div>
        </div>

        <div class="row">
            <!-- Warranty Expires -->
            <div class="col-md-6 mb-3 non-consumable-fields" style="{{ isset($item) && $item->type == 1 ? '' : 'display:none;' }}">
                <label for="warranty-expires" class="form-label">Warranty Expires</label>
                <input type="date"
                    class="form-control"
                    id="warranty-expires"
                    name="warranty_expires"
                    value="{{ isset($inventory) ? $inventory->warranty_expires : '' }}">
            </div>
        </div>

        <!-- Description (Styled Like Normal Input But Scrollable) -->
        <div class="mb-3">
            <label for="item-description" class="form-label">Description</label>
            <textarea class="form-control"
                id="item-description"
                name="description"
                rows="2"
                style="resize: none; overflow-y: auto; max-height: 80px;">{{ isset($item) ? $item->description : '' }}</textarea>
        </div>

        <!-- Simple Picture Upload -->
        <div class="mb-3">
            <label class="form-label">Item Picture</label>
            <div class="border rounded p-3 text-center"
                id="picture-dropzone"
                style="cursor: pointer; min-height: 150px; display: flex; align-items: center; justify-content: center;">

                <input type="file"
                    id="item-picture"
                    name="picture"
                    accept="image/*"
                    onchange="previewPicture(event)"
                    style="display:none;">

                <img id="picture-preview"
                    src="{{ isset($item) && $item->picture ? asset('storage/' . $item->picture) : '' }}"
                    class="img-fluid rounded"
                    style="max-height: 120px; {{ isset($item) && $item->picture ? '' : 'display:none;' }}">

                <div id="picture-placeholder"
                    class="text-muted"
                    style="{{ isset($item) && $item->picture ? 'display:none;' : '' }}">
                    Click or drag to upload picture
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style=" background-color: rgb(43, 45, 87);">Save Item</button>
    </div>
</form>

<script>
    (function() {
        // Everything inside this IIFE is local
        const dropzone = document.getElementById('picture-dropzone');
        const inputFile = document.getElementById('item-picture');
        const preview = document.getElementById('picture-preview');
        const placeholder = document.getElementById('picture-placeholder');

        if (!dropzone) return; // safety check

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
            }
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
    function setupNonConsumableToggle() {
        let typeSelect = document.getElementById('item-type');
        let nonConsumableFields = document.querySelectorAll('.non-consumable-fields');

        if (!typeSelect) return; // safety check

        typeSelect.addEventListener('change', function() {
            if (this.value === '1') {
                nonConsumableFields.forEach(field => field.style.display = 'block');
            } else {
                nonConsumableFields.forEach(field => field.style.display = 'none');
            }
        });
    }

    // Call the function after the form is loaded
    setupNonConsumableToggle();
</script>