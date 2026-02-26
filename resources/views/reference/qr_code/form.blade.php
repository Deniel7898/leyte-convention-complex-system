<div class="modal-header" style="background-color: rgb(43, 45, 87);">
    <h5 class="modal-title text-white">View Item</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

    <div class="row">
        <!-- Item Name -->
        <div class="col-md-6 mb-3">
            <label class="form-label">Item Name</label>
            <p class="form-control-plaintext">{{ $item->name ?? '--' }}</p>
        </div>

        <!-- Unit -->
        <div class="col-md-6 mb-3">
            <label class="form-label">Unit</label>
            <p class="form-control-plaintext">{{ $item->unit->name ?? '--' }}</p>
        </div>
    </div>

    <div class="row">
        <!-- Quantity -->
        <div class="col-md-6 mb-3">
            <label class="form-label">Quantity</label>
            <p class="form-control-plaintext">{{ $item->quantity ?? '--' }}</p>
        </div>

        <!-- Type -->
        <div class="col-md-6 mb-3">
            <label class="form-label">Type</label>
            <p class="form-control-plaintext">
                @if($item->type == 0) Consumable @else Non-Consumable @endif
            </p>
        </div>
    </div>

    <div class="row">
        <!-- Category -->
        <div class="col-md-6 mb-3">
            <label class="form-label">Category</label>
            <p class="form-control-plaintext">{{ $item->category->name ?? '--' }}</p>
        </div>

        <!-- Received Date -->
        <div class="col-md-6 mb-3">
            <label class="form-label">Received Date</label>
            <p class="form-control-plaintext">
                {{ $inventory->received_date ? \Carbon\Carbon::parse($inventory->received_date)->format('M d, Y') : '--' }}
            </p>
        </div>
    </div>

    <!-- Warranty Expires -->
    @if($item->type == 1)
    <div class="mb-3">
        <label class="form-label">Warranty Expires</label>
        <p class="form-control-plaintext">
            {{ $inventory->warranty_expires ? \Carbon\Carbon::parse($inventory->warranty_expires)->format('M d, Y') : '--' }}
        </p>
    </div>
    @endif

    <!-- Description -->
    <div class="mb-3">
        <label class="form-label">Description</label>
        <p class="form-control-plaintext">{{ $item->description ?? '--' }}</p>
    </div>

    <!-- Picture -->
    <div class="mb-3">
        <label class="form-label">Item Picture</label>
        <div class="border rounded p-3 text-center" style="min-height: 150px; display: flex; align-items: center; justify-content: center;">
            @if($item->picture)
            <img src="{{ asset('storage/' . $item->picture) }}" class="img-fluid rounded" style="max-height: 120px;">
            @else
            <span class="text-muted">No Picture</span>
            @endif
        </div>
    </div>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>