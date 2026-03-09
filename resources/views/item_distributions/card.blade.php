<div class="card shadow-sm rounded-3 mb-2 pt-2 h-100 border-0 card-styles">
    <div class="d-flex align-items-start">

        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-center mb-1 px-2">

                <!-- Service Type Badge at the end -->
                @if($item->type == 0)
                <span class="badge bg-primary-subtle text-primary"><i class="bi bi-send-check text-primary me-2" style="font-size: 15px;"></i>Distribution</span>
                @elseif($item->type == 1)
                <span class="badge bg-warning-subtle text-orange"><i class="bi bi-box-seam text-orange me-2" style="font-size: 15px;"></i>Borrow</span>
                @endif

                <div class="ms-auto text-center">
                    <div class="dropdown">

                        <!-- Return Item (only for borrowed) -->
                        @if($item->status === 'borrowed')
                        <button type="button"
                            title="Return Item"
                            class="btn p-0 border-0 bg-transparent text-success mb-1 return-item"
                            data-url="{{ route('item_distributions.return', $item->id) }}"
                            data-item="{{ $item->item->name ?? 'N/A' }}"
                            data-qr="{{ $item->qrCode->code ?? 'N/A' }}"
                            data-status="{{ $item->status ?? 'N/A' }}"
                            data-description="{{ $item->description ?? 'N/A' }}">

                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="currentColor" class="bi bi-box-arrow-in-left" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M10 3.5a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 1 1 0v2A1.5 1.5 0 0 1 9.5 14h-8A1.5 1.5 0 0 1 0 12.5v-9A1.5 1.5 0 0 1 1.5 2h8A1.5 1.5 0 0 1 11 3.5v2a.5.5 0 0 1-1 0z" />
                                <path fill-rule="evenodd" d="M4.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H14.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708z" />
                            </svg>
                        </button>
                        @endif

                        <button class="btn p-0 border-0 bg-transparent text-gray" type="button" id="actionMenu{{ $item->id }}" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenu{{ $item->id }}">
                            <li>
                                <button type="button" class="dropdown-item text-primary edit" data-url="{{ route('item_distributions.show', ['item_distribution' => $item->id]) }}" title="View Item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye me-2">
                                        <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    View
                                </button>
                            </li>
                            <li>
                                <button type="button" title="Edit Distribution" class="dropdown-item d-flex align-items-center text-gray edit" data-url="{{ route('item_distributions.edit', $item->id) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen me-2">
                                        <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                                    </svg>
                                    Edit
                                </button>
                            </li>
                            <li>
                                <button type="button" title="Delete Distribution" class="dropdown-item d-flex align-items-center text-danger delete" data-url="{{ route('item_distributions.destroy', ['item_distribution' => $item->id]) }}">
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
            </div>

            <div class="px-3">
                <!-- Item Name -->
                <h6 class="fw-bold">{{ $item->item?->name }}</h6>
                <p class="mb-1 text-muted small">{{ $item->description ?? 'No description' }}</p>
                <hr class="my-1" />
                <small class="text-muted d-flex justify-content-between">
                    <span>Quantity</span>
                    <span class="fw-bold text-primary">{{ $item->quantity }}</span>
                </small>
                <small class="d-flex justify-content-between">
                    <span>Status</span>
                    @php
                    $status = $item->status ?? 'unknown';
                    $statusClasses = [
                    'distributed' => 'text-success',
                    'borrowed' => 'text-orange',
                    'partial' => 'text-orange',
                    'returned' => 'text-success',
                    ];
                    $class = $statusClasses[strtolower($status)] ?? 'bg-secondary-subtle text-secondary';
                    $label = ucfirst($status);
                    @endphp
                    <span class="badge {{ $class }}">{{ $label }}</span>
                </small>
            </div>
        </div>
    </div>
</div>