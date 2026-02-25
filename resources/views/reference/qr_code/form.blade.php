<form method="POST">

    <div class="modal-header" style=" background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($category) ? 'Edit' : 'Generate' }} QR Code</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        @csrf
        @if(isset($category))
        @method('PUT')
        @endif

        <div class="mb-4">
            <label for="category-name" class="col-form-label">Item Name: Optional</label>
            <input type="text"
                class="form-control"
                id="category-name"
                name="name"
                value="{{ isset($category) ? $category->name : '' }}">
        </div>
    </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style=" background-color: rgb(43, 45, 87);">Save changes</button>
    </div>
</form>