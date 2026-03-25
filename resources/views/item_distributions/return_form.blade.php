<form action="{{ route('item_distributions.returnItem', $distribution->id) }}"
    method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('POST')

    <div class="modal-header" style="background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">
            Return Item
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <!-- Hidden input for current page segment -->
        <input type="hidden" name="page" id="currentPageInput" value="{{ request()->segment(1) ?? 'inventory' }}">

        <!-- Item Name -->
        <div class="mb-3">
            <label class="form-label fw-bold">Item Name</label>
            <input type="text" class="form-control"
                value="{{ $distribution->inventory->item->name ?? '' }}"
                readonly>
        </div>

        <!-- QR Code -->
        <div class="mb-3">
            <label class="form-label fw-bold">QR Code</label>
            <input type="text" class="form-control"
                value="{{ $distribution->inventory->qrCode->code ?? 'N/A' }}"
                readonly>
        </div>

        <!-- Borrower -->
        <div class="mb-3">
            <label class="form-label fw-bold">Borrower</label>
            <input type="text" class="form-control"
                value="{{ $distribution->department_or_borrower ?? '' }}"
                readonly>
        </div>

        <!-- Borrowed Date -->
        <div class="mb-3">
            <label class="form-label fw-bold">Borrowed Date</label>
            <input type="text" class="form-control"
                value="{{ isset($distribution->distribution_date) ? \Carbon\Carbon::parse($distribution->distribution_date)->format('M j, Y') : '' }}"
                readonly>
        </div>

        <!-- Returned Date -->
        <div class="mb-3">
            <label for="returned_date" class="form-label fw-bold">Returned Date</label>
            <input type="date" class="form-control" id="returned_date" name="returned_date" value="{{ date('Y-m-d') }}"
                min="{{ isset($distribution->distribution_date) ? \Carbon\Carbon::parse($distribution->distribution_date)->format('Y-m-d') : date('Y-m-d') }}" required>
        </div>

        <!-- Notes -->
        <div class="mb-3">
            <label for="notes" class="form-label fw-bold">Notes</label>
            <textarea class="form-control" id="notes" name="notes" rows="1"
                placeholder="Condition of the item upon return"></textarea>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancel
        </button>

        <button type="submit" class="btn text-white"
            style="background-color: rgb(43, 45, 87);">
            Confirm Return
        </button>
    </div>

</form>