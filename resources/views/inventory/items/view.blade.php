<div class="modal-header" style="background-color: rgb(43, 45, 87);">
    <h5 class="modal-title text-white">View Item</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <div class="container-fluid">

        <!-- Received Date -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Received Date</div>
            <div class="col-8">
                {{ $inventory && $inventory->received_date 
                    ? \Carbon\Carbon::parse($inventory->received_date)->format('F j, Y') 
                    : 'N/A' }}
            </div>
        </div>

        <!-- Item Name -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Item Name</div>
            <div class="col-8">{{ $inventory->item->name ?? 'N/A' }}</div>
        </div>

        <!-- Unit -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Unit</div>
            <div class="col-8">{{ $inventory->item->unit->name ?? 'N/A' }}</div>
        </div>

        <!-- Category -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Category</div>
            <div class="col-8">{{ $inventory->item->category->name ?? 'N/A' }}</div>
        </div>

        <!-- Item Status -->
        @php
        // Get the latest distribution status or default to 'available'
        $latestDistribution = $inventory->itemDistributions->last();
        $status = $latestDistribution?->status ?? 'available';

        // Define classes for each status
        $statusClasses = [
        'distributed' => 'bg-primary-subtle text-primary',
        'borrowed' => 'bg-warning-subtle text-orange',
        'partial' => 'bg-warning-subtle text-orange',
        'returned' => 'bg-info-subtle text-info',
        'received' => 'bg-success-subtle text-success',
        'pending' => 'bg-secondary-subtle text-secondary',
        'available' => 'bg-success-subtle text-success',
        ];

        // Pick class, fallback to secondary
        $class = $statusClasses[strtolower($status)] ?? 'bg-secondary-subtle text-secondary';
        @endphp

        <div class="row py-2 border-bottom align-items-center">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Item Status</div>
            <div class="col-8">
                <span class="px-2 py-1 rounded {{ $class }}" style="font-weight: 500; font-size: 0.8rem;">
                    {{ ucfirst($status) }}
                </span>
            </div>
        </div>

        <!-- QR Code -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Item QR Code</div>
            <div class="col-8">
                <span class="px-2 rounded border bg-light">{{ $inventory->qrCode->code ?? 'N/A' }}</span>
            </div>

        </div>

        <!-- Holder/Deparment -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Holder/Department</div>
            <div class="col-8">{{ $inventory->holder ?? 'N/A' }}</div>
        </div>

        <!-- Recorded By -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Recorded By</div>
            <div class="col-8" style="white-space: pre-wrap;">{{ $inventory->users->name ?? 'N/A' }}</div>
        </div>

        <!-- Description -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Description</div>
            <div class="col-8" style="white-space: pre-wrap;">{{ $inventory->item->description ?? 'No Description' }}</div>
        </div>

        <!-- Pictures & QR Code Section -->
        <div class="row py-2 border-bottom">
            <!-- Labels Row -->
            <div class="col-6 fw-bold text-center" style="color: rgb(43, 45, 87);">Item Picture</div>
            <div class="col-6 fw-bold text-center" style="color: rgb(43, 45, 87);">Item QR Code</div>

            <!-- Images Row -->
            <div class="col-6 d-flex justify-content-center mt-2">
                @if(isset($inventory->item) && $inventory->item->picture)
                <img src="{{ asset('storage/' . $inventory->item->picture) }}"
                    class="img-fluid rounded inventoryPicture"
                    style="max-height: 170px; cursor: pointer;">
                @else
                <div class="text-muted">No picture available</div>
                @endif
            </div>

            <div class="col-6 d-flex justify-content-center mt-2">
                @if(isset($inventory->qrCode->qr_picture) && $inventory->qrCode->qr_picture)
                <img src="{{ asset('storage/' . $inventory->qrCode->qr_picture ?? '') }}"
                    class="img-fluid rounded inventoryQRCode"
                    style="max-height: 170px; cursor: pointer;">
                @else
                <div class="text-muted">No QR Code</div>
                @endif
            </div>
        </div>

        <!-- Overlay -->
        <div id="pictureOverlay" style="
    display:none;
    position: fixed;
    top:0; left:0; width:100%; height:100%;
    background: rgba(0,0,0,0.8);
    justify-content: center;
    align-items: center;
    z-index: 1050;
">
            <span id="closeOverlay" style="
        position: absolute; top:20px; right:30px; font-size: 2rem; color:white; cursor:pointer;
    ">&times;</span>
            <img id="overlayImage" src="" style="max-height: 90vh; max-width: 90vw; border-radius: 8px;">
        </div>

        <script>
            document.addEventListener('click', function(e) {
                const overlay = document.getElementById('pictureOverlay');
                const overlayImage = document.getElementById('overlayImage');

                // Open overlay for item picture or QR code
                if (e.target && (e.target.classList.contains('inventoryPicture') || e.target.classList.contains('inventoryQRCode'))) {
                    overlay.style.display = 'flex';
                    overlayImage.src = e.target.src; // dynamically load the clicked image
                }

                // Close overlay if close button clicked
                if (e.target && e.target.id === 'closeOverlay') {
                    overlay.style.display = 'none';
                }

                // Close overlay if click outside image
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