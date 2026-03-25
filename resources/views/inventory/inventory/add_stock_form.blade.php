<div class="modal-header" style="background-color: rgb(43, 45, 87);">
    <h5 class="modal-title text-white">Restock Item</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <form method="POST" action="{{ route('inventory.add_stock') }}" id="restockForm">
        @csrf

        <input type="hidden" name="item_id" value="{{ $selectedItem->id ?? '' }}">
        <!-- Hidden input for current page segment -->
        <input type="hidden" name="page" id="currentPageInput" value="{{ request()->segment(1) ?? 'inventory' }}">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label bold-label">Category</label>
                <input type="text" class="form-control text-muted"
                    value="{{ $selectedItem->category->name ?? '' }}" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label bold-label">Item</label>
                <input type="text" class="form-control text-muted"
                    value="{{ $selectedItem->name ?? '' }}" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label bold-label">Type</label>
                <input type="text" class="form-control text-muted"
                    value="{{ $selectedItem->type ?? '' }}" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label bold-label">Unit</label>
                <input type="text" class="form-control text-muted"
                    value="{{ $selectedItem->unit->name ?? '' }}" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label for="quantity" class="form-label required">Quantity</label>
                <input type="number" id="quantity" name="quantity" class="form-control" min="1" required maxlength="3" pattern="\d{1,3}" placeholder="Enter Quantity (max-999)">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label bold-label">Supplier</label>
                <input type="text" class="form-control text-muted"
                    value="{{ $selectedItem->supplier ?? '' }}" readonly>
            </div>

            <div class="mb-3">
                <label for="stock-notes" class="form-label bold-label">Notes</label>
                <textarea id="stock-notes" name="notes" class="form-control" rows="1"
                    style="resize: none; overflow-y: auto; max-height: 80px;"
                    placeholder="Optional notes"></textarea>
            </div>
        </div>

        <input type="hidden" name="added_by" value="{{ auth()->id() }}">

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Restock</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('quantity').addEventListener('input', function() {
        if (this.value.length > 3) {
            this.value = this.value.slice(0, 3); // Trim to 3 digits
        }
    });
</script>