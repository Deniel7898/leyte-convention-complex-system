<form action="{{ isset($unit) ? route('units.update', $unit->id) : route('units.store') }}" method="POST">
    <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">
            {{ isset($unit) ? 'Edit' : 'Add' }} Unit
        </h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body">
        @csrf
        @if(isset($unit))
            @method('PUT')
        @endif

        <div class="mb-4">
            <label for="unit-name" class="col-form-label">Name:</label>
            <input type="text"
                   class="form-control"
                   id="unit-name"
                   name="name"
                   value="{{ isset($unit) ? $unit->name : '' }}">
        </div>

        <div class="mb-4">
            <label for="unit-description" class="col-form-label">Description:</label>
            <textarea class="form-control"
                      id="unit-description"
                      name="description">{{ isset($unit) ? $unit->description : '' }}</textarea>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Close
        </button>
        <button type="submit" class="btn btn-primary">
            Save changes
        </button>
    </div>
</form>
