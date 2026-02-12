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
            <input type="text"
                class="form-control"
                id="item-name"
                name="name"
                value="{{ isset($item) ? $item->name : '' }}"
                required>
        </div>

        <!-- Category -->
        <div class="mb-3">
            <label for="item-category" class="form-label">Category</label>
            <select class="form-select" id="item-category" name="category_id" required>
                <option value="">Select Category</option>
                <option value="0" {{ (isset($item) && $item->category_id == 0) ? 'selected' : '' }}>
                    Consumable
                </option>
                <option value="1" {{ (isset($item) && $item->category_id == 1) ? 'selected' : '' }}>
                    Non-Consumable
                </option>
            </select>
        </div>

        <!-- Quantity -->
        @if(!isset($item))
        <div class="mb-3">
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

        <!-- Unit -->
        <div class="mb-3">
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

        <!-- Picture Upload -->
        <div class="mb-3">
            <label class="form-label">Item Picture</label>
            <div class="border rounded p-3 text-center"
                style="cursor: pointer;"
                onclick="document.getElementById('item-image').click();">

                <input type="file"
                    id="item-image"
                    name="image"
                    accept="image/*"
                    onchange="previewImage(event)"
                    style="display:none;">

                <img id="image-preview"
                    src="{{ isset($item) && $item->image ? asset('storage/' . $item->image) : '' }}"
                    class="img-fluid rounded mb-2"
                    style="max-height: 150px; {{ isset($item) && $item->image ? '' : 'display:none;' }}">

                <div class="text-muted small">
                    Click to upload image
                </div>
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

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style=" background-color: rgb(43, 45, 87);">Save Item</button>
    </div>
</form>

<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('image-preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>