<form class="form-submit" action="{{ isset($inventory) ? route('inventory.update', $inventory->id) : route('inventory.store') }}" method="POST">
    <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">{{ isset($inventory) ? 'Edit' : 'Add' }} inventory</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        @csrf
        @if(isset($inventory))
        @method('PUT')
        @endif
        
        <!-- <div class="mb-3">
            <label for="inventory-serial_number" class="col-form-label">Serial Number:</label>
            <input type="text" class="form-control" id="inventory-serial_number" name="serial_number" value="{{ isset($inventory) ? $inventory->serial_number : '' }}">
        </div> -->
       
        <div class="mb-3">
            <label for="inventory-description" class="col-form-label">Description: (optional)</label>
            <textarea class="form-control" id="inventory-description" name="description">{{ isset($inventory) ? $inventory->description : '' }}</textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</form>