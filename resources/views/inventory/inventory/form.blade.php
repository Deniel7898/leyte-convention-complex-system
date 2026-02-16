<form action="{{ isset($inventory) ? route('inventory.update', $inventory->id) : route('inventory.store') }}"
    method="POST"
    enctype="multipart/form-data">

    @csrf
    @if(isset($inventory))
    @method('PUT')
    @endif

    <div class="modal-header" style=" background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($inventory) ? 'Edit inventory' : 'Add inventory' }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">

        <!-- inventory Name -->
        <div class="mb-3">
            @if(!isset($inventory))
            <label for="inventory-name" class="form-label">Inventory Name</label>
            <input type="text"
                class="form-control"
                id="inventory-name"
                name="name"
                value="{{ isset($inventory) ? $inventory->name : '' }}"
                required>
            @else
            <input type="hidden" name="name" value="{{ $inventory->name }}">
            @endif
        </div>

        <div class="row">
            <!-- Type -->
            <div class="col-md-6 mb-3">
                @if(!isset($inventory))
                <label for="inventory-type" class="form-label">Type</label>
                <select class="form-select" id="inventory-type" name="type" required>
                    <option value="">Select type</option>
                    <option value="0" {{ (isset($inventory) && $inventory->type == 0) ? 'selected' : '' }}>Consumable</option>
                    <option value="1" {{ (isset($inventory) && $inventory->type == 1) ? 'selected' : '' }}>Non-Consumable</option>
                </select>
                @else
                <input type="hidden" name="type" value="{{ $inventory->type }}">
                @endif
            </div>

            <!-- Quantity -->
            <div class="col-md-6 mb-3">
                @if(!isset($inventory))
                <label for="inventory-quantity" class="form-label">Quantity</label>
                <input type="number"
                    class="form-control"
                    id="inventory-quantity"
                    name="quantity"
                    min="1"
                    required>
                @else
                <input type="hidden" name="quantity" value="{{ $inventory->quantity }}">
                @endif
            </div>
        </div>

        <div class="row">
            <!-- Unit -->
            <div class="col-md-6 mb-3">
                @if(!isset($inventory))
                <label for="inventory-unit" class="form-label">Unit</label>
                <select class="form-select" id="inventory-unit" name="unit_id" required>
                    <option value="">Select Unit</option>
                    @foreach ($units as $unit)
                    <option value="{{ $unit->id }}"
                        {{ (isset($inventory) && $inventory->unit_id == $unit->id) ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                    @endforeach
                </select>
                @else
                <input type="hidden" name="unit" value="{{ $inventory->unit }}">
                @endif
            </div>

            <!-- Category -->
            <div class="col-md-6 mb-3">
                @if(!isset($inventory))
                <label for="inventory-category" class="form-label">Category</label>
                <select class="form-select" id="inventory-category" name="category_id" required>
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ (isset($inventory) && $inventory->category_id == $category->id) ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
                @else
                <input type="hidden" name="category" value="{{ $inventory->category }}">
                @endif
            </div>
        </div>

        <div class="row">
            <!-- Status -->
            <div class="col-md-6 mb-3">
                <label for="inventory-status" class="form-label">Status</label>
                <select class="form-select" id="inventory-status" name="status" required>
                    <option value="">Select Status</option>
                    <option value="1" {{ (isset($inventory) && $inventory->item->status == 1) ? 'selected' : '' }}>
                        Available
                    </option>
                    <option value="0" {{ (isset($inventory) && $inventory->item->status == 0) ? 'selected' : '' }}>
                        Not Available
                    </option>
                </select>
            </div>

            <!-- Warranty Expires -->
            <div class="col-md-6 mb-3 non-consumable-fields"
                style="{{ isset($inventory) && $inventory->item->type == 1 ? '' : 'display:none;' }}">
                <label for="warranty-expires" class="form-label">Warranty Expires</label>
                <input type="date"
                    class="form-control"
                    id="warranty-expires"
                    name="warranty_expires"
                    value="{{ $inventory->warranty_expires ?? '' }}">
            </div>
        </div>

        <!-- Description (Styled Like Normal Input But Scrollable) -->
        <div class="mb-3">
            @if(!isset($inventory))
            <label for="inventory-description" class="form-label">Description</label>
            <textarea class="form-control"
                id="inventory-description"
                name="description"
                rows="2"
                style="resize: none; overflow-y: auto; max-height: 80px;">{{ isset($inventory) ? $inventory->description : '' }}</textarea>
            @else
            <input type="hidden" name="description" value="{{ $inventory->description }}">
            @endif
        </div>

        <!-- Simple Picture Upload -->
        <div class="mb-3">
            @if(!isset($inventory))
            <label class="form-label">Inventory Picture</label>
            <div class="border rounded p-3 text-center"
                id="picture-dropzone"
                style="cursor: pointer; min-height: 150px; display: flex; align-inventory: center; justify-content: center;">

                <input type="file"
                    id="inventory-picture"
                    name="picture"
                    accept="image/*"
                    onchange="previewPicture(event)"
                    style="display:none;">

                <img id="picture-preview"
                    src="{{ isset($inventory) && $inventory->picture ? asset('storage/' . $inventory->picture) : '' }}"
                    class="img-fluid rounded"
                    style="max-height: 120px; {{ isset($inventory) && $inventory->picture ? '' : 'display:none;' }}">

                <div id="picture-placeholder"
                    class="text-muted"
                    style="{{ isset($inventory) && $inventory->picture ? 'display:none;' : '' }}">
                    Click or drag to upload picture
                </div>
            </div>
            @else
            <input type="hidden" name="picture" value="{{ $inventory->picture }}">
            @endif
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style=" background-color: rgb(43, 45, 87);">Save inventory</button>
    </div>
</form>

<script>
    (function() {
        // Everything inside this IIFE is local
        const dropzone = document.getElementById('picture-dropzone');
        const inputFile = document.getElementById('inventory-picture');
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