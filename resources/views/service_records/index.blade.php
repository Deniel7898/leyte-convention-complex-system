@extends('layouts.app')

@section('page_title', 'Item Service Records')

@section('content')

    <div class="p-3 bg-white border rounded-3 mt-30">
        <div class="row align-items-center gx-3">
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-search" viewBox="0 0 16 16">
                            <path
                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                        </svg>
                    </span>
                    <input type="search" id="service-search" class="form-control"
                        placeholder="Search by item name or QR ID...">
                </div>
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
                <select id="type-filter" class="form-select">
                    <option>All Service Type</option>
                    <option>Maintenance</option>
                    <option>Installation</option>
                    <option>Inspection</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Service Record Cards (Pending Only) -->
    <div id="service-record-cards" style="overflow-x: auto; white-space: nowrap; padding-top: 1rem;">
        @if($service_records->whereNull('completed_date')->count() > 0)
            <div class="mb-1" id="cards-row" style="display: flex; flex-wrap: nowrap; gap: 0.1rem;">
                @foreach($service_records as $record)
                    @if(is_null($record->completed_date))
                        @include('service_records.card', ['record' => $record])
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <div class="card shadow-sm border-0 rounded-4 card-styles mt-3">
        <div class="card-body p-0">
            <div class="table-responsive rounded-4" style="min-height: 215px;">
                <table class="table align-middle table-hover" id="serviceRecords_table">
                    <thead class="bg-light">
                        <tr class="text-uppercase text-muted small">
                            <th>{{ __('#') }}</th>
                            <th class="text-center">{{ __('QR Code') }}</th>
                            <th>{{ __('Item') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Service Type') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Schedule Date') }}</th>
                            <th>{{ __('Technician') }}</th>
                            <th class="text-center">{{ __('Picture') }}</th>
                            <th>{{ __('Completed Date') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="serviceRecords-table-body" class="text-muted small">
                        {!!$serviceRecords_table!!}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="serviceRecords_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loading-spinner">
        <div class="spinner"></div>
    </div>

    <script>
        window.liveSearchUrl = "{{ route('service_records.liveSearch') }}";
    </script>

@endsection