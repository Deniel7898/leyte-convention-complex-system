<form action="{{ isset($inventory) ? route('inventory.update', $inventory->id) : route('inventory.store') }}"
    method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($item))
    @method('PUT')
    @endif

    <div class="modal-header" style="background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($item) ? 'Edit' : 'Add' }} Item</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <div class="row">
            <!-- Hidden input for current page segment -->
            <input type="hidden" name="page" id="currentPageInput" value="{{ request()->segment(1) ?? 'inventory' }}">
            
             <!-- Category -->
            <div class="col-md-6 mb-3">
                <label for="item-category" class="form-label required">Category</label>
                <select class="form-select" id="item-category" name="category_id" required>
                    <option value="" disabled {{ !isset($item) ? 'selected' : '' }} hidden>Select Category</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        data-type="{{ $category->type }}"
                        {{ isset($item) && $item->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Item Name -->
            <div class="col-md-6 mb-3">
                <label for="item-name" class="form-label required">Item Name</label>
                <input type="text" class="form-control" id="item-name" name="name"
                    value="{{ isset($item) ? $item->name : '' }}" required>
            </div>

            <!-- Type -->
            @if(!isset($item))
            <div class="col-md-6 mb-3">
                <label for="item-type" class="form-label required">Type</label>
                <select class="form-select" id="item-type" name="type" required>
                    <option value="" disabled {{ !isset($item) ? 'selected' : '' }} hidden>Select Type</option>
                    <option value="consumable" {{ isset($item) && $item->type === 'consumable' ? 'selected' : '' }}>Consumable</option>
                    <option value="non-consumable" {{ isset($item) && $item->type === 'non-consumable' ? 'selected' : '' }}>Non-Consumable</option>
                </select>
            </div>
            @endif

            <!-- Unit -->
            <div class="col-md-6 mb-3">
                <label for="item-unit" class="form-label required">Unit</label>
                <select class="form-select" id="item-unit" name="unit_id" required>
                    <option value="" disabled {{ !isset($item) ? 'selected' : '' }} hidden>Select Unit</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ isset($item) && $item->unit_id == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Total Stock -->
            @if(!isset($item))
            <div class="col-md-6 mb-3">
                <label for="item-total-stock" class="form-label required">Quantity</label>
                <input type="number" id="quantity" class="form-control" id="item-total-stock" name="total_stock"
                    value="{{ isset($item) ? $item->total_stock : '' }}" min="1" required maxlength="3" pattern="\d{1,3}" placeholder="Enter Quantity (max-999)">
            </div>
            @endif

            <!-- Supplier -->
            <div class="col-md-6 mb-3">
                <label for="item-supplier" class="form-label bold-label">Supplier</label>
                <input type="text" class="form-control" id="item-supplier" name="supplier"
                    value="{{ isset($item) ? $item->supplier : '' }}">
            </div>

            <!-- Description (full width) -->
            <div class="col-12 mb-3">
                <label for="item-description" class="form-label bold-label">Description</label>
                <textarea class="form-control" id="item-description" name="description" rows="1"
                    style="resize: none; overflow-y: auto; max-height: 80px;">{{ isset($item) ? $item->description : '' }}</textarea>
            </div>

            <!-- Picture Upload (full width) -->
            <div class="col-12 mb-3">
                <label class="form-labe bold-label">Item Picture</label>
                <div class="border rounded p-3 text-center" id="picture-dropzone"
                    style="cursor: pointer; min-height: 150px; display: flex; align-items: center; justify-content: center;">
                    <input type="file" id="item-picture" name="picture" accept="image/*" style="display:none;">
                    <img id="picture-preview"
                        src="{{ isset($item) && $item->picture ? asset('storage/' . $item->picture) : '' }}"
                        class="img-fluid rounded" style="max-height: 120px; {{ isset($item) && $item->picture ? '' : 'display:none;' }}">
                    <div id="picture-placeholder"
                        class="text-muted" style="{{ isset($item) && $item->picture ? 'display:none;' : '' }}">
                        Click or drag to upload picture
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="created_by" value="{{ isset($item) ? $item->created_by : auth()->id() }}">
        <input type="hidden" name="updated_by" value="{{ auth()->id() }}">
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Save Item</button>
    </div>
</form>

<script>
    (function() {
        const dropzone = document.getElementById('picture-dropzone');
        const inputFile = document.getElementById('item-picture');
        const preview = document.getElementById('picture-preview');
        const placeholder = document.getElementById('picture-placeholder');

        if (!dropzone) return;

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
            }
            reader.readAsDataURL(file);
        }
    })();
</script>
<script>
    document.getElementById('item-category').addEventListener('change', function() {
        let selectedOption = this.options[this.selectedIndex];
        let categoryType = selectedOption.getAttribute('data-type');

        let typeSelect = document.getElementById('item-type');

        if (categoryType) {
            typeSelect.value = categoryType;
        }
    });
</script>
<script>
    document.getElementById('quantity').addEventListener('input', function() {
        if (this.value.length > 3) {
            this.value = this.value.slice(0, 3); // Trim to 3 digits
        }
    });
</script>