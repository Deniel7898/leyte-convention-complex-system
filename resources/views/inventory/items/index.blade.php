@extends('layouts.app')

@section('page_title', 'Item Details')

@section('content')

    <a href="{{ route('inventory.index') }}" class="fw-bold my-4 d-flex align-items-center gap-2"
        style="color: rgb(43, 45, 87);">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left"
            viewBox="0 0 16 16">
            <path fill-rule="evenodd"
                d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
        </svg>
        Back to Inventory
    </a>

    <!-- Include the Item Card  -->
    <div id="items_cards_container">
        @include('inventory.items.item_card', ['item' => $item])
    </div>

    <!-- Actions Buttons  -->
    <div id="action_buttons_container">
        @include('inventory.items.action_buttons')
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
                                <th class="text-center">QR Code</th>
                                <th>Holder / Borrower</th>
                                <th>Date Assigned</th>
                                <th>Due Date</th>
                                <th>Status</th>
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
        document.addEventListener('click', function (e) {
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