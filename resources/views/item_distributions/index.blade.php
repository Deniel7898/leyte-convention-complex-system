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
                        <th>{{ __('Remarks') }}</th>
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