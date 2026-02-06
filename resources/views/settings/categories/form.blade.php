<form action = "{{ isset($category) ? route('categories.update', $category->id) : route('categories.store') }}" method="POST">
    <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">{{ isset($category) ? 'Edit' : 'Add' }} Category</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        @csrf
        @if(isset($category))
            @method('PUT')
        @endif
        <div class="mb-3">
            <label for="category-name" class="col-form-label">Name:</label>
            <input type="text" class="form-control" id="category-name" name="name" value="{{ isset($category) ? $category->name : '' }}">
        </div>
        <div class="mb-3">
            <label for="category-description" class="col-form-label">Description:</label>
            <textarea class="form-control" id="category-description" name="description">{{ isset($category) ? $category->description : '' }}</textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</form>