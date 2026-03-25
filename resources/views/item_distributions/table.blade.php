@if($itemDistributions->count() > 0)
    @foreach($itemDistributions as $itemDistribution)
        <tr class="text-start">
            <td>
                <p>{{ $loop->iteration }}</p>
            </td>
            <td style="padding:0; margin:0; text-align:center">
                @php
                    $qr = $itemDistribution->inventory?->qrCode
                        ?? $itemDistribution->inventory?->item?->qrCode
                        ?? null;
                @endphp

                @if(!empty($qr))
                    <img src="{{ asset('storage/' . $qr->qr_picture) }}" alt="{{ $qr->code }}" class="clickable-image"
                        data-full="{{ asset('storage/' . $qr->qr_picture) }}" style="width:40px; height:auto; cursor:pointer;">
                    <br>
                    <small>{{ $qr->code }}</small>
                @else
                    <span class="text-muted">QR N/A</span>
                @endif
            </td>
            <td>

                @php
                    $itemName = $itemDistribution->inventory?->item->name ?? $itemDistribution->item?->name;
                @endphp

                @if(!empty($itemName))
                    <span style="cursor: pointer;" data-bs-toggle="popover" data-bs-placement="top"
                        data-bs-content="{{ $itemName }}">
                        {{ Str::limit($itemName, 20, '...') }}
                    </span>
                @else
                    <span>--</span>
                @endif

                @if(!empty($itemDistribution->department_or_borrower))
                    <br>
                    <small class="text-muted" style="cursor: pointer;" data-bs-toggle="popover" data-bs-placement="top"
                        data-bs-content="{{ $itemDistribution->department_or_borrower }}">
                        {{ Str::limit($itemDistribution->department_or_borrower, 15, '...') }}
                    </small>
                @endif

            </td>
            <td>{{ $itemDistribution->inventory?->item->category->name ?? $itemDistribution->item?->category->name ?? '--' }}
            </td>
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

            <!-- Universal Lightbox (one per page) -->
            <div id="universalLightbox"
                style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
                                                    background: rgba(0,0,0,0.8); justify-content:center; align-items:center; z-index:1050;">
                <button id="universalLightboxClose"
                    style="position:absolute; top:20px; right:20px; background:none;
                                                        border:none; color:white; font-size:2rem; cursor:pointer;">&times;</button>
                <img id="universalLightboxImg" src="" style="max-width:90%; max-height:90%; border-radius:8px;">
            </div>

            <script>
                document.addEventListener('click', e => {
                    const target = e.target;

                    // Check if a clickable image was clicked
                    if (target.classList.contains('clickable-image')) {
                        const lightbox = document.getElementById('universalLightbox');
                        const lightboxImg = document.getElementById('universalLightboxImg');
                        lightboxImg.src = target.dataset.full;
                        lightbox.style.display = 'flex';
                    }

                    // Close lightbox if close button clicked
                    if (target.id === 'universalLightboxClose') {
                        const lightbox = document.getElementById('universalLightbox');
                        const lightboxImg = document.getElementById('universalLightboxImg');
                        lightbox.style.display = 'none';
                        lightboxImg.src = '';
                    }

                    // Close lightbox if background clicked
                    if (target.id === 'universalLightbox') {
                        const lightboxImg = document.getElementById('universalLightboxImg');
                        target.style.display = 'none';
                        lightboxImg.src = '';
                    }
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
                        'issued' => 'bg-primary-subtle text-primary',
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
                @if(!empty($itemDistribution->notes))
                    <p style="margin:0; cursor: pointer;" data-bs-toggle="popover" data-bs-placement="top"
                        data-bs-content="{{ $itemDistribution->notes }}">
                        {{ Str::limit($itemDistribution->notes, 15, '...') }}
                    </p>
                @else
                    <p>--</p>
                @endif
            </td>
            <td class="text-center">
                <div class="dropdown">
                    <!-- Return Item (only for borrowed) -->
                    @if($itemDistribution->status === 'borrowed' || $itemDistribution->status === 'issued')
                        <button type="button" title="Return Item" class="btn p-0 border-0 bg-transparent text-success return-item"
                            data-url="{{ route('item_distributions.return_form', $itemDistribution->id) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="currentColor"
                                class="bi bi-arrow-repeat" viewBox="0 0 16 16">
                                <path
                                    d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9" />
                                <path fill-rule="evenodd"
                                    d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z" />
                            </svg>
                        </button>
                    @endif

                    <button class="btn p-0 border-0 bg-transparent text-gray" title="Actions" type="button"
                        id="actionMenu{{ $itemDistribution->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i> <!-- 3-dot icon -->
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenu{{ $itemDistribution->id }}">
                        <!-- View Item -->
                        <li>
                            <button type="button" class="dropdown-item text-primary edit"
                                data-url="{{ route('item_distributions.show', $itemDistribution->id) }}" title="View Item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-eye me-1">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                View
                            </button>
                        </li>

                        <!-- Edit Item -->
                        @if(!in_array($itemDistribution->status, ['completed', 'returned']))
                            <li>
                                <button type="button" class="dropdown-item text-gray edit"
                                    data-url="{{ route('item_distributions.edit', $itemDistribution->id) }}" title="Edit Item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-square-pen me-1">
                                        <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path
                                            d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z">
                                        </path>
                                    </svg>
                                    Edit
                                </button>
                            </li>
                        @endif
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