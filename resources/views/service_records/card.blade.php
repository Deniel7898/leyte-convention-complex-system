<div class="col-md-3" id="service-card-{{ $record->id }}">
    <div class="card shadow-sm rounded-3 pt-2 mb-1 h-100 border-0 card-styles">
        <div class="d-flex align-items-start">
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1 px-2">
                    <!-- Service Type Badge -->
                    @if($record->type == 0)
                    <span class="badge bg-warning-subtle text-orange">
                        <i class="bi bi-tools text-orange me-2" style="font-size: 15px;"></i>Maintenance
                    </span>
                    @elseif($record->type == 1)
                    <span class="badge bg-primary-subtle text-primary">
                        <i class="bi bi-box-seam text-primary me-2" style="font-size: 15px;"></i>Installation
                    </span>
                    @endif

                    <!-- Complete / Undo Buttons -->
                    <div class="ms-auto text-center">
                        <button type="button"
                            class="btn p-0 border-0 bg-transparent text-success complete-service"
                            data-url="{{ route('service_records.complete', $record->id) }}"
                            data-item="{{ $record->item->item->name ?? 'N/A' }}"
                            data-type="{{ $record->type }}"
                            data-qr="{{ $record->inventoryNonConsumable->qrCode->code ?? 'N/A' }}"
                            data-schedule="{{ \Carbon\Carbon::parse($record->schedule_date)->format('F d, Y') }}"
                            data-person="{{ $record->encharge_person }}">
                            <i class="bi bi-check-circle"></i>
                        </button>

                        <!-- 3-dot menu button -->
                        <button class="btn p-0 border-0 bg-transparent text-gray" type="button" id="actionMenu{{ $record->id }}" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>

                        <!-- Dropdown menu -->
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenu{{ $record->id }}">
                            <li>
                                <button type="button" title="View Service" class="dropdown-item d-flex align-items-center text-primary edit" data-url="{{ route('service_records.show', $record->id) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye me-2">
                                        <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    View
                                </button>
                            </li>
                            <li>
                                <button type="button"
                                    title="Edit Item"
                                    class="dropdown-item d-flex align-items-center text-gray edit"
                                    data-url="{{ route('service_records.edit', $record->id) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen me-2">
                                        <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                                    </svg>
                                    Edit
                                </button>
                            </li>
                            <li>
                                <button type="button"
                                    title="Delete Item"
                                    class="dropdown-item d-flex align-items-center text-danger delete"
                                    data-url="{{ route('service_records.destroy', $record->id) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 me-2">
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
                </div>

                <!-- Card Body -->
                <div class="px-3">
                    <h6 class="fw-bold mb-0">{{ $record->item->item->name ?? 'No Item' }}</h6>
                    <p class="mb-1 text-muted small">{{ $record->description ?? 'No description' }}</p>
                    <hr class="my-1" />
                    <small class="d-flex justify-content-between">
                        <span>Quantity</span>
                        <span class="fw-bold text-primary">{{ $record->quantity ?? 1 }}</span>
                    </small>
                    <small class="d-flex justify-content-between">
                        <span>Schedule Date</span>
                        <span class="text-muted small">
                            {{ $record->schedule_date 
                                ? \Carbon\Carbon::parse($record->schedule_date)->format('F j, Y') 
                                : 'No Schedule' }}
                        </span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>