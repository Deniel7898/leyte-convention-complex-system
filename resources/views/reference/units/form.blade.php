<form action="{{ isset($unit) ? route('units.update', $unit->id) : route('units.store') }}" method="POST">

    <div class="modal-header" style=" background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($unit) ? 'Edit' : 'Add' }} Unit</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        @csrf
        @if(isset($unit))
        @method('PUT')
        @endif
        <div class="mb-3">
            <label for="unit-name" class="col-form-label">Name:</label>
            <input type="text" class="form-control" id="unit-name" name="name" value="{{ isset($unit) ? $unit->name : '' }}">
        </div>
        <div class="mb-3">
            <label for="unit-description" class="col-form-label">Description: (optional)</label>
            <textarea class="form-control" id="unit-description" name="description">{{ isset($unit) ? $unit->description : '' }}</textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style=" background-color: rgb(43, 45, 87);">Save changes</button>
    </div>
</form>