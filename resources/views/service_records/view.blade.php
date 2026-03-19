<div class="modal-header" style="background-color: rgb(43, 45, 87);">
    <h5 class="modal-title text-white">View Item Service</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <div class="container-fluid">

        <!-- Service Type -->
        @php
        $serviceType = $service_record->type ?? null;
        $badgeClasses = [
        'maintenance' => 'bg-warning-subtle text-warning', // Maintenance
        'installation' => 'bg-primary-subtle text-primary', // Installation
        'inspection' => 'bg-primary-subtle text-primary', // Inspection
        ];
        $badgeClass = $badgeClasses[$serviceType] ?? 'bg-secondary-subtle text-secondary';
        $serviceLabel = $serviceType === 'maintenance' ? 'Maintenance' : ($serviceType === 'installation' ? 'Installation' : ($serviceType === 'inspection' ? 'Inspection' : 'N/A'));
        @endphp

        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Service Type</div>
            <div class="col-8">
                <span class="px-2 rounded {{ $badgeClass }} fw-bold" style="font-size: 0.9rem;">
                    {{ $serviceLabel }}
                </span>
            </div>
        </div>

        <!-- Selected Item -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Item Name</div>
            <div class="col-8">{{ $service_record->inventory->item->name ?? 'N/A' }}</div>
        </div>

        <!-- Unit -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Unit</div>
            <div class="col-8">{{ $service_record->inventory->item->unit->name ?? 'N/A' }}</div>
        </div>

        <!-- Category -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Category</div>
            <div class="col-8">{{ $service_record->inventory->category->item->name ?? 'N/A' }}</div>
        </div>

        <!-- Quantity -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Quantity</div>
            <div class="col-8">{{ 1 }}</div>
        </div>

        <!-- Service Status -->
        <div class="row py-2 border-bottom align-items-center">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Service Status</div>
            <div class="col-8">
                @if(isset($service_record) && $service_record->completed_date)
                <span class="badge bg-success-subtle text-success">Completed!</span>
                @else
                @php
                // Optional: map status to a bootstrap color
                $statusColors = [
                'scheduled' => 'bg-primary-subtle text-primary',
                'under repair' => 'bg-warning-subtle text-orange',
                'cancelled' => 'bg-danger-subtle text-danger',
                ];

                $status = $service_record->status ?? 'pending';
                $badgeClass = $statusColors[$status] ?? 'bg-secondary-subtle text-secondary';
                @endphp
                <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                @endif
            </div>
        </div>

        <!-- QR Code -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Item QR Code</div>
            <div class="col-8">
                <span class="px-2 rounded border bg-light">{{ $service_record->inventory->qrCode->code ?? 'N/A' }}</span>
            </div>

        </div>

        <!-- Schedule Date -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Schedule Date</div>
            <div class="col-8">
                {{ isset($service_record) && $service_record->service_date
            ? \Carbon\Carbon::parse($service_record->service_date)->format('F j, Y')
            : 'N/A' }}
            </div>
        </div>

        <!-- Description -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Description</div>
            <div class="col-8" style="white-space: pre-wrap;">{{ $service_record->description ?? 'N/A' }}</div>
        </div>

        <!-- Person in Charge -->
        <div class="row py-2 border-bottom">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Person in Charge</div>
            <div class="col-8">{{ $service_record->technician ?? 'N/A' }}</div>
        </div>

        <!-- Completed Date (only if exists) -->
        @if(isset($service_record) && $service_record->completed_date)
        <div class="row py-2 border-bottom align-items-center">
            <div class="col-4 fw-bold" style="color: rgb(43, 45, 87);">Completed Date</div>
            <div class="col-8">
                <span class="px-2 rounded bg-success-subtle text-success fw-bold" style="font-size: 0.8rem;">
                    {{ \Carbon\Carbon::parse($service_record->completed_date)->format('F j, Y') }}
                </span>
            </div>
        </div>
        @endif

        <!-- Pictures & QR Code Side by Side -->
        <div class="row py-2 border-bottom">
            <!-- Labels Row -->
            <div class="col-6 fw-bold text-center" style="color: rgb(43, 45, 87);">Item Picture</div>
            <div class="col-6 fw-bold text-center" style="color: rgb(43, 45, 87);">Item QR Code</div>

            <!-- Images Row -->
            <div class="col-6 d-flex justify-content-center mt-2">
                @if(isset($service_record) && $service_record->picture)
                <img src="{{ asset('storage/' . $service_record->picture) }}"
                    class="img-fluid rounded clickableImage"
                    style="max-height: 180px; cursor: pointer;"
                    alt="{{ $service_record->name }} Picture">
                @else
                <div class="text-muted">No picture available</div>
                @endif
            </div>

            <div class="col-6 d-flex justify-content-center mt-2">
                @if(isset($service_record->inventory->qrCode?->qr_picture) && $service_record->inventory->qrCode->qr_picture)
                <img src="{{ asset('storage/' . $service_record->inventory->qrCode->qr_picture) }}"
                    class="img-fluid rounded clickableImage"
                    style="max-height: 180px; cursor: pointer;"
                    alt="{{ $service_record->name }} QR Code">
                @else
                <div class="text-muted">No QR Code</div>
                @endif
            </div>
        </div>

        <!-- Overlay for Both Images -->
        <div id="imageOverlay" style="
    display:none;
    position: fixed;
    top:0; left:0; width:100%; height:100%;
    background: rgba(0,0,0,0.8);
    justify-content: center;
    align-items: center;
    z-index: 1050;
">
            <span id="closeImageOverlay" style="
        position: absolute; top:20px; right:30px; font-size: 2rem; color:white; cursor:pointer;
    ">&times;</span>
            <img id="overlayImage" src="" style="max-height: 90vh; max-width: 90vw; border-radius: 8px;">
        </div>

        <script>
            document.addEventListener('click', function(e) {
                const overlay = document.getElementById('imageOverlay');
                const overlayImage = document.getElementById('overlayImage');

                // Open overlay for item picture or QR code
                if (e.target && e.target.classList.contains('clickableImage')) {
                    overlay.style.display = 'flex';
                    overlayImage.src = e.target.src;
                }

                // Close overlay if close button clicked
                if (e.target && e.target.id === 'closeImageOverlay') {
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