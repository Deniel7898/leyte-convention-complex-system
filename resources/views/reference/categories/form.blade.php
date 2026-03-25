<form action="{{ isset($category) ? route('categories.update', $category->id) : route('categories.store') }}" method="POST">

    <div class="modal-header" style="background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($category) ? 'Edit' : 'Add' }} Category</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        @csrf
        @if(isset($category))
        @method('PUT')
        @endif

        <!-- Name -->
        <div class="mb-3">
            <label for="category-name" class="col-form-label">
                Name <span style="color: black;">*</span>
            </label>
            <input type="text"
                class="form-control"
                id="category-name"
                name="name"
                value="{{ isset($category) ? $category->name : '' }}">
        </div>

        <!-- Type Dropdown -->
        <div class="mb-3">
            <label for="category-type" class="col-form-label">
                Type <span style="color: black;">*</span>
            </label>
            <select class="form-select" id="category-type" name="type">
                <option value="consumable" {{ (isset($category) && $category->type == 'consumable') ? 'selected' : '' }}>Consumable</option>
                <option value="non-consumable" {{ (isset($category) && $category->type == 'non-consumable') ? 'selected' : '' }}>Non-Consumable</option>
            </select>
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label for="category-description" class="col-form-label">Description</label>
            <textarea class="form-control"
                id="category-description"
                name="description">{{ isset($category) ? $category->description : '' }}</textarea>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Save changes</button>
    </div>
</form>