<div class="modal-header" style="background-color: rgb(43, 45, 87);">
    <h5 class="modal-title text-white">View Item Distribution</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <div class="container-fluid">

        <!-- Distribution Date -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Distribution Date</div>
            <div class="col-8">
                {{ isset($itemDistribution) ? \Carbon\Carbon::parse($itemDistribution->distribution_date)->format('Y-m-d') : 'N/A' }}
            </div>
        </div>

        <!-- Distribution Type -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Distribution Type</div>
            <div class="col-8">{{ isset($itemDistribution) ? ($itemDistribution->type == 0 ? 'Distribution' : 'Borrow') : ($selectedItem->type == 0 ? 'Distribution' : 'Borrow') }}</div>
        </div>

        <!-- Distribution Status -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Distribution Status</div>
            <div class="col-8">
                @php
                $status = $itemDistribution->status ?? 'unknown';
                $statusClasses = [
                'distributed' => 'bg-success-subtle text-success',
                'borrowed' => 'bg-warning-subtle text-orange',
                'partial' => 'bg-warning-subtle text-orange',
                'returned' => 'bg-success-subtle text-success',
                'pending' => 'bg-secondary-subtle text-secondary',
                ];
                $class = $statusClasses[strtolower($status)] ?? 'bg-secondary-subtle text-secondary';
                $label = ucfirst($status);
                @endphp
                <span class="badge {{ $class }}">{{ $label }}</span>
            </div>
        </div>

        <!-- Item Name -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Item Name</div>
            <div class="col-8">{{ $itemDistribution->inventory->item->name ?? 'N/A' }}</div>
        </div>

        <!-- Unit -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Unit</div>
            <div class="col-8">{{ $itemDistribution->inventory->item->unit->name ?? 'N/A' }}</div>
        </div>

        <!-- Category -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Category</div>
            <div class="col-8">{{ $itemDistribution->inventory->item->category->name ?? 'N/A' }}</div>
        </div>

        <!-- Quantity -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Quantity</div>
            <div class="col-8">{{ 1 }}</div>
        </div>

        <!-- Description -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Description</div>
            <div class="col-8" style="white-space: pre-wrap;">{{ $itemDistribution->description ?? 'N/A' }}</div>
        </div>

        <!-- Distributed By -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Distributed By</div>
            <div class="col-8">{{ $itemDistribution->createdBy->last_name ?? 'N/A' }}</div>
        </div>

        <!-- Item QR Code -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Item QR Code</div>
            <div class="col-8">
                @if($itemDistribution->inventory->qrCode?->qr_picture)
                <img src="{{ asset('storage/' . $itemDistribution->inventory->qrCode->qr_picture) }}"
                    alt="{{ $itemDistribution->inventory->item->name }} QR Code"
                    class="img-fluid rounded inventoryQRCode"
                    style="max-height: 120px; cursor: pointer;">
                @else
                <span class="px-2 rounded border bg-light">{{ $itemDistribution->inventory->qrCode->code ?? 'N/A' }}</span>
                @endif
            </div>
        </div>

        <!-- QR Code Overlay -->
        <div id="qrOverlay" style="
            display:none;
            position: fixed;
            top:0; left:0; width:100%; height:100%;
            background: rgba(0,0,0,0.8);
            justify-content: center;
            align-items: center;
            z-index: 1050;">
            <span id="closeQROverlay" style="
                position: absolute; top:20px; right:30px; font-size: 2rem; color:white; cursor:pointer;">&times;</span>
            <img id="qrOverlayImage" src="" style="max-height: 90vh; max-width: 90vw; border-radius: 8px;">
        </div>

        <script>
            document.addEventListener('click', function(e) {
                const overlay = document.getElementById('qrOverlay');
                const overlayImage = document.getElementById('qrOverlayImage');

                // Open overlay only for QR code
                if (e.target && e.target.classList.contains('inventoryQRCode')) {
                    overlay.style.display = 'flex';
                    overlayImage.src = e.target.src;
                }

                // Close overlay if close button clicked
                if (e.target && e.target.id === 'closeQROverlay') {
                    overlay.style.display = 'none';
                }

                // Close overlay if click outside the image
                if (e.target === overlay) {
                    overlay.style.display = 'none';
                }
            });
        </script>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>