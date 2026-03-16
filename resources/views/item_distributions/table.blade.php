@if($itemDistributions->count() > 0)
@foreach($itemDistributions as $itemDistribution)
<tr class="text-start">
    <td>
        <p>{{ $loop->iteration }}</p>
    </td>
    <td>{{ $itemDistribution->inventory?->item->name ?? $itemDistribution->item?->name ?? '--' }}</td>
    <td>{{ $itemDistribution->inventory?->item->category->name ?? $itemDistribution->item?->category->name ?? '--' }}</td>
    <td>{{ $itemDistribution->inventory?->item->unit->name ?? $itemDistribution->item?->unit->name ?? '--' }}</td>
    <td>
        <p>{{ $itemDistribution->quantity ?? 0 }}</p>
    </td>
    <td>
        {{ $itemDistribution->distribution_date && $itemDistribution->distribution_date != '--'
            ? \Carbon\Carbon::parse($itemDistribution->distribution_date)->format('M d, Y')
                : '--' }}
    </td>
    <td>
        <p>{{ $itemDistribution->type ?? '--' }}</p>
    </td>
    <td style="padding:0; margin:0; vertical-align:top; text-align:center">
        @if(!empty($itemDistribution->inventory?->qrCode))
        <img
            src="{{ asset('storage/' . $itemDistribution->inventory->qrCode->qr_picture) }}"
            alt="{{ $itemDistribution->inventory->qrCode->code }}"
            class="clickable-image"
            data-full="{{ asset('storage/' . $itemDistribution->inventory->qrCode->qr_picture) }}"
            style="width:40px; height:auto; cursor:pointer;">
        <br>
        <small>{{ $itemDistribution->inventory->qrCode->code }}</small>
        @else
        <span class="text-muted">QR N/A</span>
        @endif
    </td>

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
    <td>
        @php
        $status = $itemDistribution->status ?? 'available';

        // Map statuses to badge classes
        $statusClasses = [
        'completed' => 'bg-success-subtle text-success',
        'distributed' => 'bg-success-subtle text-success',
        'borrowed' => 'bg-warning-subtle text-orange',
        'partial' => 'bg-warning-subtle text-orange',
        'returned' => 'bg-success-subtle text-success',
        'pending' => 'bg-secondary-subtle text-secondary',
        'available' => 'bg-success-subtle text-success',
        ];

        // Get class for current status, fallback to secondary if unknown
        $class = $statusClasses[strtolower($status)] ?? 'bg-secondary-subtle text-secondary';
        @endphp

        <span class="badge {{ $class }}">
            {{ ucwords($status) }}
        </span>
    </td>
    <td>
        {{ $itemDistribution->due_date && $itemDistribution->due_date != '--'
            ? \Carbon\Carbon::parse($itemDistribution->due_date)->format('M d, Y')
                : '--' }}
    </td>
    <td>
        <p>{{ $itemDistribution->notes ?? '--' }}</p>
    </td>
    <td class="text-center">
        <div class="dropdown">
            <!-- Return Item (only for borrowed) -->
            @if($itemDistribution->status === 'borrowed')
            <button type="button"
                title="Return Item"
                class="btn p-0 border-0 bg-transparent text-success return-item"
                data-url="{{ route('item_distributions.return_form', $itemDistribution->id) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="currentColor" class="bi bi-box-arrow-in-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10 3.5a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 1 1 0v2A1.5 1.5 0 0 1 9.5 14h-8A1.5 1.5 0 0 1 0 12.5v-9A1.5 1.5 0 0 1 1.5 2h8A1.5 1.5 0 0 1 11 3.5v2a.5.5 0 0 1-1 0z" />
                    <path fill-rule="evenodd" d="M4.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H14.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708z" />
                </svg>
            </button>
            @endif

            <button class="btn p-0 border-0 bg-transparent text-gray" title="Actions" type="button" id="actionMenu{{ $itemDistribution->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-three-dots-vertical"></i> <!-- 3-dot icon -->
            </button>

            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenu{{ $itemDistribution->id }}">

                <!-- Show only if status is returned or distributed -->
                @if(isset($itemDistribution) && in_array($itemDistribution->status, ['returned', 'distributed']))
                <li>
                    <button type="button"
                        title="Undo Completion"
                        class="dropdown-item d-flex align-items-center text-warning edit"
                        data-url="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-arrow-counterclockwise me-2" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2z" />
                            <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466" />
                        </svg>
                        Undo Complete
                    </button>
                </li>
                @endif

                <!-- View Item -->
                <li>
                    <button type="button" class="dropdown-item text-primary edit" data-url="{{ route('item_distributions.show', $itemDistribution->id) }}" title="View Item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye me-2">
                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        View
                    </button>
                </li>

                <!-- Edit Item -->
                <li>
                    <button type="button" class="dropdown-item text-gray edit" data-url="{{ route('item_distributions.edit', $itemDistribution->id) }}" title="Edit Item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen me-2">
                            <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                        </svg>
                        Edit
                    </button>
                </li>

                <!-- Delete Item -->
                <li>
                    <button type="button" class="dropdown-item text-danger delete" data-url="{{ route('item_distributions.destroy', $itemDistribution->id) }}" title="Delete Item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2 me-2">
                            <path d="M3 6h18"></path>
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            <line x1="10" x2="10" y1="11" y2="17"></line>
                            <line x1="14" x2="14" y1="11" y2="17"></line>
                        </svg>
                        Delete
                    </button>
                </li>
            </ul>
        </div>
    </td>
</tr>
@endforeach
@else
<tr>
    <td colspan="12" class="text-center py-3">{{ __('No Distribution found.') }}</td>
</tr>
@endif