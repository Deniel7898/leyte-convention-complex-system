@extends('layouts.app')

@section('page_title', 'Items Record')

@section('content')

<!-- Modern Navigation -->
<nav class="nav-modern mt-15 mb-3">
    <a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.*') ? '' : '' }}">{{ __('Inventory') }}</a>
    <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.*') ? 'active' : '' }}">{{ __('Items') }}</a>
</nav>

<div class="p-3 bg-white border rounded-3">
    <div class="row align-items-center gx-3">
        <div class="col">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                    </svg>
                </span>
                <input
                    type="search"
                    class="form-control border-start-0 ps-1"
                    placeholder="Search by item name or QR ID..." />
            </div>
        </div>


        <div class="col-auto" style="min-width: 140px;">
            <select class="form-select">
                <option selected>All Categories</option>
                <option>Category 1</option>
                <option>Category 2</option>
            </select>
        </div>

        <div class="col-auto" style="min-width: 140px;">
            <select class="form-select">
                <option selected>All Status</option>
                <option>Active</option>
                <option>Used</option>
                <option>Expired</option>
            </select>
        </div>

        <div class="col-auto">
            <div class="col-auto add-item" data-url="{{route('items.create')}}">
                <div class="col-auto add-item" data-url="{{route('items.create')}}">
                    <button class="btn px-4 text-white" style="background-color: hsl(237, 34%, 30%);" onmouseover="this.style.backgroundColor='hsl(237, 34%, 40%)'" onmouseout="this.style.backgroundColor='hsl(237, 34%, 30%)'">
                        + Add Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="text-muted small mt-2">
        Showing 8 of 8 items
    </div>
</div>

<div class="card-styles mt-3">
    <div class="card-style-3 mb-30">
        <div class="card-content">
            <div class="table-responsive">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="fw-800">{{ __('Item List') }}</h6>
                </div>
                <table class="table" id="items_table">
                    <thead>
                        <tr class="text-center">
                            <th>
                                <h6>{{ __('#') }}</h6>
                            </th>
                            <th>
                                <h6>{{ __('Name') }}</h6>
                            </th>
                            <th>
                                <h6>{{ __('Availability') }}</h6>
                            </th>
                            <th>
                                <h6>{{ __('Quantity') }}</h6>
                            </th>
                            <th>
                                <h6>{{ __('Remaining') }}</h6>
                            </th>
                            <th>
                                <h6>{{ __('Unit') }}</h6>
                            </th>
                            <th>
                                <h6>{{ __('Category') }}</h6>
                            </th>
                            <th>
                                <h6>{{ __('Description') }}</h6>
                            </th>
                            <th>
                                <h6>{{ __('Picture') }}</h6>
                            </th>
                            <th>
                                <h6>{{ __('Actions') }}</h6>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {!!$items_table!!}
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="items_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="items_modal_view" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-view">
            <!-- Your modal header, body, footer here -->
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div id="loading-spinner">
    <div class="spinner"></div>
</div>
@endsection