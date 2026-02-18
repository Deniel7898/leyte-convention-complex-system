<form action="{{ isset($item) ? route('viewItem.update', $item->id) : route('viewItem.store') }}"
    method="POST">
    @csrf
    @if(isset($item))
    @method('PUT')
    @endif

    <div class="modal-header" style="background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($item) ? 'Edit Item' : 'Add Item' }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <!-- Item Name -->
        <div class="mb-3">
            <label for="item-item" class="col-form-label">Item:</label>
            <select class="form-control" id="item-item" name="item_id" required>
                <option value="">Select Item</option>
                @foreach ($items as $itm)
                <option value="{{ $itm->id }}"
                    @selected(isset($selectedItem) && $selectedItem->id == $itm->id)>
                    {{ $itm->name }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Hidden Fields from Selected Item -->
        <input type="hidden" id="item-type" name="type" value="{{ $selectedItem->type ?? '' }}">
        <input type="hidden" name="unit_id" value="{{ $selectedItem->unit_id ?? '' }}">
        <input type="hidden" name="category_id" value="{{ $selectedItem->category_id ?? '' }}">
        <input type="hidden" name="quantity" value="1">
        <input type="hidden" name="description" value="{{ $selectedItem->description ?? '' }}">

        <!-- Status -->
        <div class="mb-3">
            <label for="item-status" class="form-label">Status</label>
            <select class="form-select" id="item-status" name="status" required>
                <option value="">Select Status</option>
                <option value="1" {{ (isset($inventory) && $inventory->status == 1) ? 'selected' : '' }}>Available</option>
                <option value="0" {{ (isset($inventory) && $inventory->status == 0) ? 'selected' : '' }}>Not Available</option>
            </select>
        </div>

        <!-- Warranty Expires (only if non-consumable) -->
        <div class="mb-3" id="warranty-field" style="{{ isset($selectedItem) && $selectedItem->type == 1 ? '' : 'display:none;' }}">
            <label for="warranty-expires" class="form-label">Warranty Expires</label>
            <input type="date" class="form-control" id="warranty-expires" name="warranty_expires"
                value="{{ isset($inventory) ? $inventory->warranty_expires : '' }}">
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Save Item</button>
    </div>
</form>


<script>
    function handleWarrantyField() {
        const itemSelect = document.getElementById('item-item');
        const typeInput = document.getElementById('item-type');
        const warrantyField = document.getElementById('warranty-field');
        const warrantyInput = document.getElementById('warranty-expires');

        // Make sure element exists
        if (!itemSelect) return;

        // Update hidden fields + visibility
        itemSelect.addEventListener('change', function() {
            const selectedId = this.value;
            if (!selectedId) {
                // hide if no selection
                warrantyField.style.display = 'none';
                return;
            }

            const item = itemsData[selectedId] ?? null;
            if (!item) {
                warrantyField.style.display = 'none';
                return;
            }

            // Update hidden type field
            typeInput.value = item.type;

            // Show or hide warranty based on type
            if (item.type == 1) {
                warrantyField.style.display = 'block';
                // If editing, load existing; otherwise clear
                warrantyInput.value = item.warranty_expires ?? '';
            } else {
                warrantyField.style.display = 'none';
            }
        });

        // Trigger change on load in case selectedItem is non-consumable
        itemSelect.dispatchEvent(new Event('change'));
    }

    // Wrap everything so it does not redeclare global variables
    document.addEventListener('DOMContentLoaded', function() {
        handleWarrantyField();
    });
</script>