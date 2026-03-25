@if($service_records->count() > 0)
    @foreach($service_records as $service_record)
        <tr class="text-start">
            <td>{{ $loop->iteration }}</td>
            <td style="padding:0; margin:0; text-align:center">
                @if($service_record->inventory?->qrCode)
                    <img src="{{ asset('storage/' . $service_record->inventory->qrCode->qr_picture) }}"
                        alt="{{ $service_record->inventory->qrCode->code }}" width="40" class="clickable-image"
                        style="cursor:pointer;"
                        data-full="{{ asset('storage/' . $service_record->inventory->qrCode->qr_picture) }}">
                    <br>
                    <small>{{ $service_record->inventory->qrCode->code }}</small>
                @else
                    QR
                @endif
            </td>
            <td>
                {{ $service_record->inventory->item->name ?? '--' }}
                @if(!empty($service_record->description))
                    <br>
                    <small class="text-muted" style="cursor: pointer;" data-bs-toggle="popover" data-bs-placement="top"
                        data-bs-content="{{ $service_record->description }}">
                        {{ Str::limit($service_record->description, 15, '...') }}
                    </small>
                @endif
            </td>
            <td>
                <i class="bi bi-tag me-1"></i>
                {{ $service_record->inventory->item->category->name ?? '--' }}
            </td>
            <td>
                @if($service_record->type == 'maintenance')
                    <span class="badge bg-warning-subtle text-orange">Maintenance</span>
                @elseif($service_record->type == 'installation')
                    <span class="badge bg-primary-subtle text-primary">Installation</span>
                @elseif($service_record->type == 'inspection')
                    <span class="badge bg-info-subtle text-secondary">Inspection</span>
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
                @elseif($service_record->status === 'under repair')
                    <span class="badge bg-warning-subtle text-orange">Under Repair</span>
                @elseif($service_record->status === 'scheduled')
                    <span class="badge bg-primary-subtle text-primary">Scheduled</span>
                @elseif($service_record->status === 'cancelled')
                    <span class="badge bg-danger-subtle text-danger">Cancelled</span>
                @endif
            </td>
            <td>
                {{ $service_record->service_date
                    ? \Carbon\Carbon::parse($service_record->service_date)->format('M d, Y')
                    : '--' }}
            </td>
            <td>{{ $service_record->technician ?? '--' }}</td>
            <td style="padding:0; margin:0; text-align:center">
                @if($service_record->picture)
                    <img src="{{ asset('storage/' . $service_record->picture) }}"
                        alt="{{ $service_record->inventory->item->name ?? 'N/A' }}" width="50" class="clickable-image img-square"
                        style="cursor: pointer;" data-full="{{ asset('storage/' . $service_record->picture) }}">
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
                        <button type="button" title="Complete Service"
                            class="btn p-0 border-0 bg-transparent text-success complete-service"
                            data-url="{{ route('service_records.show_service', $service_record->id) }}">
                            <i class="bi bi-check-circle"></i>
                        </button>
                    @endif

                    {{-- Dropdown Actions --}}
                    <div class="dropdown">
                        <button class="btn p-0 border-0 bg-transparent text-gray" type="button"
                            id="actionMenu{{ $service_record->id }}" data-bs-toggle="dropdown" aria-expanded="false"
                            title="Actions">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                            aria-labelledby="actionMenu{{ $service_record->id }}">

                            {{-- View --}}
                            <li>
                                <button type="button" title="View Item"
                                    class="dropdown-item d-flex align-items-center text-primary edit"
                                    data-url="{{ route('service_records.show', $service_record->id) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-eye w-4 h-4 me-1">
                                        <path
                                            d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                        </path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    View
                                </button>
                            </li>

                            {{-- Edit --}}
                            @if($service_record->status !== 'completed')
                                <li>
                                    <button type="button" title="Edit Item"
                                        class="dropdown-item d-flex align-items-center text-gray edit"
                                        data-url="{{ route('service_records.edit', $service_record->id) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="lucide lucide-square-pen w-4 h-4 me-1">
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
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="11" class="text-center py-3">{{ __('No Service Records found.') }}</td>
    </tr>
@endif