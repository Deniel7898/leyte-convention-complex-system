@extends('layouts.app')

@section('page_title', 'Inventory Management')

@section('content')

<div class="p-3 bg-white border rounded-3 mt-30 mb-3">
    <div class="row gx-2 align-items-center">

        <!-- Categories Pills (scrollable) -->
        <div class="col-auto flex-grow-1">
            <ul class="nav nav-pills flex-nowrap overflow-auto" id="categoryTabs" role="tablist" style="gap: 0.5rem;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active text-white bg-dark" id="all-tab" data-bs-toggle="tab" data-category="All" type="button" role="tab">
                        All
                    </button>
                </li>
                @foreach($categories as $category)
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-dark bg-light" id="cat-{{ $category->id }}-tab" data-bs-toggle="tab" data-category="{{ $category->id }}" type="button" role="tab">
                        {{ $category->name }}
                    </button>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Search input -->
        <div class="col-auto" style="min-width: 250px;">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                    </svg>
                </span>
                <input type="search" id="inventory-search" class="form-control" placeholder="Search items..">
            </div>
        </div>

        <!-- Add Item button -->
        <div class="col-auto">
            <div class="add-inventory" data-url="{{route('inventory.create')}}">
                <button class="btn px-4 text-white" style="background-color: hsl(237, 34%, 30%)"
                    onmouseover="this.style.backgroundColor='hsl(237, 34%, 40%)'"
                    onmouseout="this.style.backgroundColor='hsl(237, 34%, 30%)'">
                    + Add Item
                </button>
            </div>
        </div>

    </div>
</div>

<style>
    /* Hover effect for pills */
    #categoryTabs .nav-link {
        transition: all 0.2s ease-in-out;
        white-space: nowrap;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.9rem;
    }

    #categoryTabs .nav-link:hover {
        background-color: #6c757d;
        /* subtle gray hover */
        color: white;
    }

    /* Active tab styling */
    #categoryTabs .nav-link.active {
        background-color: #2b2d57 !important;
        color: white !important;
    }

    /* Scrollbar for overflow on small screens */
    #categoryTabs::-webkit-scrollbar {
        height: 6px;
    }

    #categoryTabs::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 3px;
    }

    #categoryTabs::-webkit-scrollbar-thumb:hover {
        background-color: #555;
    }
</style>

<div class="card shadow-sm border-0 rounded-4 card-styles mt-3">
    <div class="card-body p-0">
        <div class="table-responsive rounded-4" style="min-height: 190px;">
            <table class="table align-middle table-hover mb-0" id="inventories_table">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted small">
                        <th>{{ __('#') }}</th>
                        <th>{{ __('Item') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Unit') }}</th>
                        <th>{{ __('Total Stock') }}</th>
                        <th>{{ __('Remaining Stock') }}</th>
                        <th class="text-center">{{ __('Qr Code') }}</th>
                        <th class="text-center">{{ __('Item Picture') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="inventory-table-body" class="text-muted small">
                    {!! $inventories_table !!}
                </tbody>
            </table>
            <div id="inventory-pagination" class="flex justify-center mb-3 ms-3">
                {{ $inventories->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal modal-large fade index 02" id="inventories_modal" tabindex="-1" aria-hidden="true">
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
    window.liveSearchUrl = "{{ route('inventory.liveSearch') }}";

    // Handle category tab clicks
    document.querySelectorAll('#categoryTabs button').forEach(button => {
        button.addEventListener('click', function() {
            // Use empty string for "All" category
            const categoryId = this.getAttribute('data-category') === 'All' ? '' : this.getAttribute('data-category');
            const searchValue = document.getElementById('inventory-search').value;

            fetch(`${window.liveSearchUrl}?category=${categoryId}&search=${searchValue}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('inventory-table-body').innerHTML = html;

                    // Update active class
                    document.querySelectorAll('#categoryTabs .nav-link').forEach(link => link.classList.remove('active', 'text-white', 'bg-dark'));
                    this.classList.add('active', 'text-white', 'bg-dark');
                });
        });
    });

    // Live search
    document.getElementById('inventory-search').addEventListener('input', function() {
        document.querySelector('#categoryTabs .active').click();
    });
</script>

@endsection