@extends('layouts.app')

@section('page_title', 'Item Distribution')

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
                    <input type="search" id="itemDistribution-search" class="form-control"
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
                <select id="dist-type-filter" class="form-select">
                    <option>All Dist. Type</option>
                    <option>Distribution</option>
                    <option>Issue</option>
                    <option>Borrow</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Item Distribution Cards -->
    <div id="item-dis-cards">
        @php
            $allowedStatuses = ['pending', 'partial', 'borrowed'];

            // Filter and group by transaction
            $transactions = $itemDistributions
                ->filter(fn($item) => in_array(strtolower($item->status ?? ''), $allowedStatuses))
                ->groupBy('transaction_id');
        @endphp

        <div id="cards-row"
            style="overflow-x: auto; white-space: nowrap; padding-top: 1rem; display: flex; flex-wrap: nowrap; gap: 0.1rem;">

            @foreach($transactions as $transactionId => $distributions)
                @foreach($distributions as $distribution)
                    @include('item_distributions.card', [
                        'distribution' => $distribution,
                        'item' => $distribution->item, // pass related item as well
                    ])
                @endforeach
            @endforeach
        </div>

        <div class="card shadow-sm border-0 rounded-4 card-styles mt-3">
            <div class="card-body p-0">
                <div class="table-responsive rounded-4" style="min-height: 215px;">
                    <table class="table align-middle table-hover" id="itemDistributions_table">
                        <thead class="bg-light">
                            <tr class="text-uppercase text-muted small">
                                <th>{{ __('#') }}</th>
                                <th class="text-center">{{ __('QR Code') }}</th>
                                <th>{{ __('Item') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Unit') }}</th>
                                <th>{{ __('Qty') }}</th>
                                <th>{{ __('Dist. Date') }}</th>
                                <th>{{ __('Dist. Type') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Due Date') }}</th>
                                <th>{{ __('Notes') }}</th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="itemDistributions-table-body" class="text-muted small">
                            {!!$itemDistributions_table!!}
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center mb-2 mx-2 px-2">
                        <span id="showLessBtn" class="clickable small fw-500 " style="display:none; cursor:pointer; color: rgb(43, 45, 87);">
                            Show Less
                        </span>

                        <div>
                            <span id="showMoreBtn" class="clickable small fw-500 " style="cursor:pointer; margin-right:10px; color: rgb(43, 45, 87);">
                                Show More
                            </span>
                            <span id="showAllBtn" class="clickable small fw-500 " style="cursor:pointer; color: rgb(43, 45, 87);">
                                Show All
                            </span>
                        </div>
                    </div>
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

        <!-- Loading Spinner -->
        <div id="loading-spinner">
            <div class="spinner"></div>
        </div>

        <script>
                window.liveSearchUrl = "{{ route('item_distributions.liveSearch') }}";
        </script>


  <script>
document.addEventListener("DOMContentLoaded", function () {

    const rows = document.querySelectorAll("#itemDistributions-table-body tr");

    let visibleCount = 20; // default
    const step = 10;

    function updateTable() {
        rows.forEach((row, index) => {
            row.style.display = index < visibleCount ? "" : "none";
        });

        // Show/Hide buttons
        document.getElementById("showMoreBtn").style.display =
            visibleCount >= rows.length ? "none" : "inline";

        document.getElementById("showLessBtn").style.display =
            visibleCount > 20 ? "inline" : "none";

        // Show/Hide Show All
        document.getElementById("showAllBtn").style.display =
            visibleCount >= rows.length ? "none" : "inline";
    }

    // Show More (+10)
    document.getElementById("showMoreBtn").addEventListener("click", function () {
        visibleCount += step;
        updateTable();
    });

    // Show Less (back to 20)
    document.getElementById("showLessBtn").addEventListener("click", function () {
        visibleCount = 20;
        updateTable();
    });

    // Show All
    document.getElementById("showAllBtn").addEventListener("click", function () {
        visibleCount = rows.length;
        updateTable();
    });

    // Initialize
    updateTable();
});
</script>
@endsection