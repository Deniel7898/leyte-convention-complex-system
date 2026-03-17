<form action="{{ isset($itemDistribution) ? route('item_distributions.update', $itemDistribution->id) : route('item_distributions.store') }}"
    method="POST">
    @csrf
    @if(isset($itemDistribution))
    @method('PUT')
    @endif

    <div class="modal-header" style="background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($itemDistribution) ? 'Edit' : 'New' }} Distribution</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">

        <!-- QR Code (read-only) -->
        <div class="mb-3">
            <label class="form-label">QR Code</label>
            <input type="text" class="form-control" value="{{ $itemDistribution->inventory->qrCode->code ?? 'N/A' }}" readonly>
        </div>

        <!-- Item Name -->
        <div class="mb-3">
            <label for="item-name" class="form-label">Item Name</label>
            <input type="text" class="form-control" id="item-name" name="name"
                value="{{ isset($item) ? $item->name : '' }}" required readonly>
        </div>

        <!-- Holder / Department -->
        <div class="mb-3">
            <label class="form-label">Holder / Department</label>
            <input type="text" class="form-control" name="department_or_borrower"
                value="{{ old('department_or_borrower', $itemDistribution->department_or_borrower ?? '') }}" required>
        </div>

        <!-- Date Assigned -->
        <div class="mb-3">
            <label class="form-label">Date Assigned</label>
            <input type="date" class="form-control" name="distribution_date"
                value="{{ old('distribution_date', $itemDistribution->distribution_date ?? date('Y-m-d')) }}" required>
        </div>

        <!-- Due Date -->
        <div class="mb-3">
            <label class="form-label">Due Date</label>
            <input type="date" class="form-control" name="due_date"
                value="{{ old('due_date', $itemDistribution->due_date ?? '') }}">
        </div>

        <!-- Notes -->
        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea class="form-control" name="notes" rows="1">{{ old('notes', $itemDistribution->notes ?? '') }}</textarea>
        </div>

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">
            Save
        </button>
    </div>
</form>
