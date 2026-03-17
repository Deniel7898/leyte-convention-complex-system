@extends('layouts.app')

@section('page_title', 'Item Details')

@section('content')

<!-- Include the Item Card  -->
<div id="items_cards_container">
    @include('inventory.items.item_card', ['item' => $item])
</div>

<!-- Item Non-Consumable Table -->
@if($item->type === 'non-consumable')
<div class="card shadow-lg rounded-4 modern-card">
    <div class="card-body p-0">
        <div class="table-responsive rounded-4">
            <table class="table align-middle table-hover mb-0" id="items_table">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted small">
                        <th>#</th>
                        <th>Status</th>
                        <th>Holder / Borrower</th>
                        <th>Date Assigned</th>
                        <th>Due Date</th>
                        <th class="text-center">QR Code</th>
                        <th>Notes</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-muted small" id="items-table-body">
                    <!-- Include non consumable items table -->
                    @include('inventory.items.non_consumable_table')
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Universal Lightbox -->
<div id="universalLightbox" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
    background: rgba(0,0,0,0.8); justify-content:center; align-items:center; z-index:1050;">
    <button id="universalLightboxClose" style="position:absolute; top:20px; right:20px; background:none;
        border:none; color:white; font-size:2rem; cursor:pointer;">&times;</button>
    <img id="universalLightboxImg" src="" style="max-width:90%; max-height:90%; border-radius:8px;">
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const lightbox = document.getElementById('universalLightbox');
        const lightboxImg = document.getElementById('universalLightboxImg');
        const closeBtn = document.getElementById('universalLightboxClose');

        // Open lightbox when any .clickable-image is clicked (event delegation)
        document.body.addEventListener('click', (e) => {
            if (e.target.classList.contains('clickable-image')) {
                const fullSrc = e.target.dataset.full;
                if (fullSrc) {
                    lightboxImg.src = fullSrc;
                    lightbox.style.display = 'flex';
                }
            }
        });

        // Close lightbox with close button
        closeBtn.addEventListener('click', () => {
            lightbox.style.display = 'none';
            lightboxImg.src = '';
        });

        // Close lightbox when clicking outside the image
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                lightbox.style.display = 'none';
                lightboxImg.src = '';
            }
        });
    });
</script>

<!-- Modal -->
<div class="modal modal-large fade index 02" id="items_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div id="loading-spinner">
    <div class="spinner"></div>
</div>

<!-- Consumable Item History -->
<div id="history_container">
    @include('inventory.items.history_table', ['item' => $item, 'history' => $history])
</div>

<script>
    document.addEventListener('click', function(e) {
        if (e.target.closest('#toggle-history')) { // checks if clicked element or its parent is the button
            const nonContainer = document.getElementById('items_table')?.closest('.card');
            const historyContainer = document.getElementById('history_container');

            if (!nonContainer || !historyContainer) return;

            const isNonVisible = nonContainer.style.display === 'none';

            // Toggle visibility
            nonContainer.style.display = isNonVisible ? 'block' : 'none';
            historyContainer.style.display = isNonVisible ? 'none' : 'block';

            // Update button text/icon
            e.target.closest('#toggle-history').innerHTML = isNonVisible ?
                '<i class="bi bi-clock-history"></i> History' :
                '<i class="bi bi-arrow-left"></i> Back to Items List';
        }
    });
</script>

@endsection