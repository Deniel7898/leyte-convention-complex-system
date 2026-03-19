@extends('layouts.app')

@section('page_title', 'Purchase Requests Management')

@section('content')

<!-- ========== title-wrapper start ========== -->
<div class="title-wrapper pt-15 mb-1">
    <div class="row align-items-center">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Left: Page Title -->
                <div class="title">
                    <div>
                        <h4>Purchase Requests</h4>
                        <p class="text-muted mb-0 text-sm">Manage and track all purchase requests.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<div class="p-3 bg-white border rounded-3">
    <div class="row align-items-center gx-3">
        <div class="col">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                    </svg>
                </span>
                <input type="search" id="pr-search" class="form-control" placeholder="Search by request ID or date...">
            </div>
        </div>

        <div class="col-auto">
            <div class="col-auto add-purchase-request" data-url="{{ route('purchase-requests.create') }}">
                <button class="btn px-4 text-white" style="background-color: hsl(237, 34%, 30%);" onmouseover="this.style.backgroundColor='hsl(237, 34%, 40%)'" onmouseout="this.style.backgroundColor='hsl(237, 34%, 30%)'">
                    + Add Request
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 card-styles mt-3">
    <div class="card-body p-0">
        <div class="table-responsive rounded-4">
            <table class="table align-middle table-hover" id="purchase_requests_table">
                <thead class="bg-light">
                    <tr class="text-uppercase pr-text-muted pr-small">
                        <th>{{ __('#') }}</th>
                        <th>{{ __('Request Date') }}</th>
                        <th>{{ __('Items Count') }}</th>
                        <th>{{ __('Created By') }}</th>
                        <th>{{ __('Created Date') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="purchase-requests-table-body" class="pr-text-muted pr-small">
                    {!! $purchase_requests_table !!}
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="purchase_requests_modal" tabindex="-1" aria-hidden="true">
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
    window.liveSearchUrl = "{{ route('purchase-requests.index') }}";
</script>

@endsection
