@if($service_records->count() > 0)
@foreach($service_records as $service_record)
<tr class="text-start">
    <td>{{ $loop->iteration }}</td>
    <td>
        {{ $service_record->inventory->item->name ?? '--' }}
        @if(!empty($service_record->inventory->item->description))
        <br>
        <small class="text-muted">{{ $service_record->inventory->item->description }}</small>
        @endif
    </td>
    <td>
        <i class="bi bi-tag me-1"></i>
        {{ $service_record->inventory->item->category->name ?? '--' }}
    </td>
    <td>
        @if($service_record->type == 0)
        <span class="badge bg-warning-subtle text-orange">Maintenance</span>
        @else
        <span class="badge bg-primary-subtle text-primary">Installation</span>
        @endif
    </td>
    <td style="padding:0; margin:0; vertical-align:top; text-align:center">
        @if($service_record->inventory->qrCode)
        <img src="{{ asset('storage/' . $service_record->inventory->qrCode->qr_picture) }}"
            alt="{{ $service_record->inventory->qrCode->code }}"
            width="40"
            class="clickable-image"
            style="cursor:pointer;"
            data-full="{{ asset('storage/' . $service_record->inventory->qrCode->qr_picture) }}">
        <br>
        <small>{{ $service_record->inventory->qrCode->code }}</small>
        @else
        QR
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
        document.addEventListener('click', e => {
            const target = e.target;

            // Open lightbox if a clickable image is clicked
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
        @if($service_record->status === 'completed')
        <span class="badge bg-success-subtle text-success">Completed</span>
        @elseif($service_record->status === 'in progress')
        <span class="badge bg-warning-subtle text-orange">In Progress</span>
        @elseif($service_record->status === 'scheduled')
        <span class="badge bg-primary-subtle text-primary">Scheduled</span>
        @elseif($service_record->status === 'cancelled')
        <span class="badge bg-danger-subtle text-danger">Cancelled</span>
        @else
        <span class="badge bg-secondary-subtle text-secondary">Pending</span>
        @endif
    </td>
    <td>
        {{ $service_record->service_date
                ? \Carbon\Carbon::parse($service_record->service_date)->format('M d, Y')
                : '--' }}
    </td>
    <td>{{ $service_record->technician ?? '--' }}</td>
    <td style="padding:0; margin:0; vertical-align:top; text-align:center">
        @if($service_record->picture)
        <img src="{{ asset('storage/' . $service_record->picture) }}"
            alt="{{ $service_record->inventory->item->name ?? 'N/A' }}"
            width="50"
            class="clickable-image"
            style="cursor: pointer;"
            data-full="{{ asset('storage/' . $service_record->picture) }}">
        @else
        <span>No Image</span>
        @endif
    </td>
    <td>
        {{ $service_record->completed_date
                ? \Carbon\Carbon::parse($service_record->completed_date)->format('M d, Y')
                : '--' }}
    </td>
    <td class="text-center">
        <div class="d-flex justify-content-center align-items-center gap-2">

            {{-- Complete Button --}}
            @if(!$service_record->completed_date)
            <button type="button"
                title="Complete Service"
                class="btn p-0 border-0 bg-transparent text-success complete-service"
                data-url="{{ route('service_records.show_service', $service_record->id) }}">
                <i class="bi bi-check-circle"></i>
            </button>
            @endif

            {{-- Dropdown Actions --}}
            <div class="dropdown">
                <button class="btn p-0 border-0 bg-transparent text-gray"
                    type="button"
                    id="actionMenu{{ $service_record->id }}"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    title="Actions">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                    aria-labelledby="actionMenu{{ $service_record->id }}">

                    {{-- Undo Completion --}}
                    @if($service_record->completed_date)
                    <li>
                    <li>
                        <button type="button"
                            title="Undo Completion"
                            class="dropdown-item d-flex align-items-center text-warning undo-completion"
                            data-url="{{ route('service_records.undo', $service_record->id) }}"
                            data-item="{{ $service_record->inventory->item->name ?? 'N/A' }}"
                            data-qr="{{ $service_record->inventory->qrCode->code ?? '' }}"
                            data-schedule="{{ \Carbon\Carbon::parse($service_record->service_date)->format('F d, Y') }}"
                            data-person="{{ $service_record->technician }}"
                            data-type="{{ $service_record->type }}">
                            <i class="bi bi-arrow-counterclockwise me-2"></i>
                            Undo Complete
                        </button>
                    </li>
                    </li>
                    @endif

                    {{-- View --}}
                    <li>
                        <button type="button"
                            title="View Item"
                            class="dropdown-item d-flex align-items-center text-primary edit"
                            data-url="{{ route('service_records.show', $service_record->id) }}">
                            <i class="bi bi-eye me-2"></i>
                            View
                        </button>
                    </li>

                    {{-- Edit --}}
                    <li>
                        <button type="button"
                            title="Edit Item"
                            class="dropdown-item d-flex align-items-center text-gray edit"
                            data-url="{{ route('service_records.edit', $service_record->id) }}">
                            <i class="bi bi-pencil-square me-2"></i>
                            Edit
                        </button>
                    </li>

                    {{-- Delete --}}
                    <li>
                        <button type="button"
                            title="Delete Item"
                            class="dropdown-item d-flex align-items-center text-danger delete"
                            data-url="{{ route('service_records.destroy', ['service_record' => $service_record->id]) }}">
                            <i class="bi bi-trash2 me-2"></i>
                            Delete
                        </button>
                    </li>

                </ul>
            </div>

        </div>
    </td>
</tr>
@endforeach
@else
<tr>
    <td colspan="11" class="text-center py-3">{{ __('No Service Records found.') }}</td>
</tr>
@endif