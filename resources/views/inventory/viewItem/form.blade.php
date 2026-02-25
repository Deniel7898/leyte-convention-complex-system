<form action="{{ isset($item) ? route('viewItem.update', $item->id) : route('viewItem.store') }}"
    method="POST">
    @csrf
    @if(isset($item))
    @method('PUT')
    @endif

    <div class="modal-header" style="background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($item) ? 'Edit' : 'Add' }} Item</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <!-- Item Name -->
        <div class="mb-3">
            <label for="general-item" class="col-form-label">Item:</label>
            <input type="text" class="form-control" id="general-item"
                value="{{ $selectedItem->name }}" readonly>
        </div>

        <!-- Hidden input to submit the item ID -->
        <input type="hidden" name="item_id" value="{{ $selectedItem->id }}">

        <!-- Receive Date (for both consumable & non-consumable) -->
        <div class="mb-3" id="receive-date">
            <label for="received-date" class="form-label">Received Date</label>
            <input type="date"
                class="form-control"
                id="received-date"
                name="received_date"
                value="{{ isset($item) ? $item->received_date : date('Y-m-d') }}">
        </div>

        <!-- Hidden Fields from Selected Item -->
        <input type="hidden" id="item-type" name="type" value="{{ $selectedItem->type ?? '' }}">
        <input type="hidden" name="unit_id" value="{{ $selectedItem->unit_id ?? '' }}">
        <input type="hidden" name="category_id" value="{{ $selectedItem->category_id ?? '' }}">
        <input type="hidden" name="quantity" value="1">
        <input type="hidden" name="description" value="{{ $selectedItem->description ?? '' }}">
        <input type="hidden" name="status" value="{{ $selectedItem->status ?? '' }}">

        <!-- Warranty Expires (only if non-consumable) -->
        <div class="mb-3" id="warranty-field" style="{{ isset($selectedItem) && $selectedItem->type == 1 ? '' : 'display:none;' }}">
            <label for="warranty-expires" class="form-label">Warranty Expires</label>
            <input type="date" class="form-control" id="warranty-expires" name="warranty_expires"
                value="{{ isset($item) ? $item->warranty_expires : '' }}">
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Save Item</button>
    </div>
</form>