<form action="{{ route('item_distributions.returnItem', $distribution->id) }}" method="POST"
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
        <div class="row">
            <!-- Hidden input for current page segment -->
            <input type="hidden" name="page" id="currentPageInput" value="{{ request()->segment(1) ?? 'inventory' }}">

            <!-- Item Name -->
            <div class="col-md-6 mb-3">
                <label class="form-label bold-label">Item Name
                </label>
                <input type="text" class="form-control" value="{{ $distribution->inventory->item->name ?? '' }}"
                    readonly>
                <input type="hidden" class="form-control" value="{{ $distribution->inventory->qrCode->code ?? 'N/A' }}">
                <small class="text-muted">
                    QR Code: {{ $distribution->inventory->qrCode->code ?? 'N/A' }}
                </small>
            </div>

            <!-- Borrower -->
            <div class="col-md-6 mb-3">
                <label class="form-label bold-label">Borrower</label>
                <input type="text" class="form-control" value="{{ $distribution->department_or_borrower ?? '' }}"
                    readonly>
            </div>

            <!-- Borrowed Date -->
            <div class="col-md-6 mb-2">
                <label class="form-label bold-label">Borrowed Date</label>
                <input type="text" class="form-control"
                    value="{{ isset($distribution->distribution_date) ? \Carbon\Carbon::parse($distribution->distribution_date)->format('M j, Y') : '' }}"
                    readonly>
            </div>

            <!-- Returned Date -->
            <div class="col-md-6 mb-3">
                <label for="returned_date" class="form-label required">Returned Date</label>
                <input type="date" class="form-control" id="returned_date" name="returned_date" min="{{ isset($distribution->distribution_date)
                ? \Carbon\Carbon::parse($distribution->distribution_date)->format('Y-m-d')
                : date('Y-m-d') }}" max="{{ date('Y-m-d') }}" value="{{ old(
                'returned_date',
                isset($distribution->distribution_date) && \Carbon\Carbon::parse($distribution->distribution_date)->gt(now())
                ? \Carbon\Carbon::parse($distribution->distribution_date)->format('Y-m-d')
                : date('Y-m-d')
            ) }}" required>
            </div>

            <!-- Notes -->
            <div class="mb-3">
                <label for="notes" class="form-label bold-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="1"
                    placeholder="Condition of the item upon return"></textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancel
        </button>

        <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">
            Confirm Return
        </button>
    </div>

</form>