@extends('layouts.app')

@section('page_title', 'Item Details')

@section('content')

<!-- Include the Item Card  -->
<div id="items_cards_container">
    @include('inventory.items.item_card', ['item' => $item])
</div>

<!-- Item History Table -->
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

<!-- Universal Lightbox (one per page) -->
<div id="universalLightbox" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
    background: rgba(0,0,0,0.8); justify-content:center; align-items:center; z-index:1050;">
    <button id="universalLightboxClose" style="position:absolute; top:20px; right:20px; background:none;
        border:none; color:white; font-size:2rem; cursor:pointer;">&times;</button>
    <img id="universalLightboxImg" src="" style="max-width:90%; max-height:90%; border-radius:8px;">
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const clickableImgs = document.querySelectorAll('.clickable-image');
        const lightbox = document.getElementById('universalLightbox');
        const lightboxImg = document.getElementById('universalLightboxImg');
        const closeBtn = document.getElementById('universalLightboxClose');

        clickableImgs.forEach(img => {
            img.addEventListener('click', () => {
                lightboxImg.src = img.dataset.full;
                lightbox.style.display = 'flex';
            });
        });

        const closeLightbox = () => {
            lightbox.style.display = 'none';
            lightboxImg.src = '';
        };

        closeBtn.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', e => {
            if (e.target === lightbox) closeLightbox();
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
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggle-history');
        const nonContainer = document.getElementById('items_table')?.closest('.card'); // the whole card
        const historyContainer = document.getElementById('history_container');

        if (toggleBtn && nonContainer && historyContainer) {
            // Hide history initially
            historyContainer.style.display = 'none';

            toggleBtn.addEventListener('click', function() {
                const isNonVisible = nonContainer.style.display === 'none';

                // Toggle visibility
                nonContainer.style.display = isNonVisible ? 'block' : 'none';
                historyContainer.style.display = isNonVisible ? 'none' : 'block';

                // Update button text/icon
                this.innerHTML = isNonVisible ?
                    '<i class="bi bi-clock-history"></i> History' :
                    '<i class="bi bi-arrow-left"></i> Back to Non-Consumable Items';
            });
        }
    });
</script>

@endsection