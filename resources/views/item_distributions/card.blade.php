<div id="distribution-card-{{ $distribution->id }}" class="mb-1" style="flex: 0 0 260px; margin-right: 1rem;">
    <div class="card shadow-sm rounded-3 mb-2 pt-2 h-100 border-0 card-styles">
        <div class="d-flex align-items-start">

            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center mb-1 px-2">

                    <!-- Service Type Badge at the end -->
                    @if($distribution->type == 'distribution')
                        <span class="badge bg-primary-subtle text-primary"><i class="bi bi-send-check text-primary me-2"
                                style="font-size: 15px;"></i>Distribution</span>
                    @elseif($distribution->type == 'borrowed')
                        <span class="badge bg-warning-subtle text-orange"><i class="bi bi-box-seam text-orange me-2"
                                style="font-size: 15px;"></i>Borrow</span>
                    @elseif($distribution->type == 'issued')
                        <span class="badge bg-primary-subtle text-primary"><i class="bi bi-box-seam text-primary me-2"
                                style="font-size: 15px;"></i>Issued</span>
                    @endif

                    <div class="ms-auto text-center">
                        <div class="dropdown">

                            <!-- Return Item (only for borrowed) -->
                            @if($distribution->status === 'borrowed')
                                <button type="button" title="Return Item"
                                    class="btn p-0 border-0 bg-transparent text-success return-item"
                                    data-url="{{ route('item_distributions.return_form', ['id' => $distribution->id]) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="currentColor"
                                        class="bi bi-arrow-repeat mb-1" viewBox="0 0 16 16">
                                        <path
                                            d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9" />
                                        <path fill-rule="evenodd"
                                            d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z" />
                                    </svg>
                                </button>
                            @endif

                            <button class="btn p-0 border-0 bg-transparent text-gray" type="button"
                                id="actionMenu{{ $distribution->id }}" data-bs-toggle="dropdown" aria-expanded="false"
                                title="Actions">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenu{{ $distribution->id }}">
                                <li>
                                    <button type="button" class="dropdown-item text-primary edit"
                                        data-url="{{ route('item_distributions.show', ['item_distribution' => $distribution->id]) }}"
                                        title="View Item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-eye me-2">
                                            <path
                                                d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                            </path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        View
                                    </button>
                                </li>
                                <li>
                                    <button type="button" title="Edit Distribution"
                                        class="dropdown-item d-flex align-items-center text-gray edit"
                                        data-url="{{ route('item_distributions.edit', $distribution->id) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-square-pen me-2">
                                            <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path
                                                d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z">
                                            </path>
                                        </svg>
                                        Edit
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="px-3">
                    <!-- Item Name -->
                    <h6 class="fw-bold">{{ $distribution->item?->name }}</h6>
                    <p class="mb-1 text-muted small">{{ $distribution->notes ?? 'No Notes' }}</p>
                    <hr class="my-1" />
                    <small class="text-muted d-flex justify-content-between">
                        <span>QR Code</span>
                        <span style="color: rgb(43, 45, 87);"
                            class="fw-600">{{ $distribution->inventory?->qrCode->code ?? "N/A" }}
                        </span>
                    </small>
                    <small class="d-flex justify-content-between">
                        <span>Borrower</span>
                        <span>{{ $distribution->department_or_borrower ?? "N/A" }}
                        </span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>