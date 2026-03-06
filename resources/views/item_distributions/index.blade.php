@extends('layouts.app')

@section('page_title', 'Item Distribution')

@section('content')

<div class="p-3 bg-white border rounded-3 mt-30">
    <div class="row align-items-center gx-3">
        <div class="col">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                    </svg>
                </span>
                <input type="search" id="itemDistribution-search" class="form-control" placeholder="Search by item name or QR ID...">
            </div>
        </div>

        <div class="col-auto" style="min-width: 140px;">
            <select id="type-filter" class="form-select">
                <option>All Item Type</option>
                <option>Consumable</option>
                <option>Non-Consumable</option>
            </select>
        </div>

        <div class="col-auto" style="min-width: 140px;">
            <select id="categories-filter" class="form-select">
                <option value="All">All Category</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-auto" style="min-width: 140px;">
            <select id="dist-type-filter" class="form-select">
                <option>All Dist. Type</option>
                <option>Distribution</option>
                <option>Borrow</option>
            </select>
        </div>

        <div class="col-auto" style="min-width: 140px;">
            <select id="status-filter" class="form-select">
                <option>All Status</option>
                <option>Distributed</option>
                <option>Borrowed</option>
                <option>Partial</option>
                <option>Pending</option>
                <option>Returned</option>
                <option>Received</option>
            </select>
        </div>

        <div class="col-auto">
            <div class="col-auto add-itemDistribution" data-url="{{route('item_distributions.create')}}">
                <button class="btn px-4 text-white" style="background-color: hsl(237, 34%, 30%);" onmouseover="this.style.backgroundColor='hsl(237, 34%, 40%)'" onmouseout="this.style.backgroundColor='hsl(237, 34%, 30%)'">
                    + New Distribution
                </button>
            </div>
        </div>
    </div>

    <!-- <div class="text-muted small mt-2">
        Showing 8 of 8 items
    </div> -->
</div>

<!-- Distribution Cards -->
<div class="row g-3 pt-15">
    @php
    // Allowed statuses
    $allowedStatuses = ['pending', 'partial', 'borrowed'];

    // Filter first, then group
    $transactions = $itemDistributions
    ->filter(function ($item) use ($allowedStatuses) {
    return in_array(strtolower($item->status ?? ''), $allowedStatuses);
    })
    ->groupBy('transaction_id');
    @endphp

    @forelse($transactions as $transactionId => $distributions)
    @php
    $item = $distributions->first(); // pick only one general item to display
    @endphp
    <div class="col-md-3">
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
                        <h6 class="fw-bold">{{ $item->item->name }}</h6>
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
    </div>
    @endforeach
</div>

<div class="card shadow-sm border-0 rounded-4 card-styles mt-3">
    <div class="card-body p-0">
        <div class="table-responsive rounded-4">
            <table class="table align-middle table-hover" id="itemDistributions_table">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted small">
                        <th>{{ __('#') }}</th>
                        <th>{{ __('Item') }}</th>
                        <th>{{ __('Item Type') }}</th>
                        <th>{{ __('Unit') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Dist. Date') }}</th>
                        <th>{{ __('Dist. Type') }}</th>
                        <th>{{ __('QR Code') }}</th>
                        <th>{{ __('Qty') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Due Date') }}</th>
                        <!-- <th>{{ __('Returned Date') }}</th> -->
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="itemDistributions-table-body" class="text-muted small">
                    {!!$itemDistributions_table!!}
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="itemDistributions_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        </div>
    </div>
</div>

<!-- View Modal -->
<!-- <div class="modal fade" id="items_modal_view" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-view">
           
        </div>
    </div>
</div> -->

<!-- Loading Spinner -->
<div id="loading-spinner">
    <div class="spinner"></div>
</div>

<script>
    window.liveSearchUrl = "{{ route('item_distributions.liveSearch') }}";
</script>

@endsection