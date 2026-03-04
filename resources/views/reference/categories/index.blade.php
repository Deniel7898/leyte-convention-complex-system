@extends('layouts.app')

@section('page_title', 'Reference Data')

@section('content')
<!-- ========== title-wrapper start ========== -->
<div class="card mb-3 shadow-sm border-0 card-styles mt-30">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center p-1">
            <!-- Left: Page Title -->
            <div class="title">
                <div>
                    <h3 class="mb-1 fw-700">Categories List</h3>
                    <p class="text-muted mb-0 text-sm">This is the reference data used for inventory and items.</p>
                </div>
            </div>

            <!-- Right: Add Button -->
            <button class="btn text-white add-category"
                title="Add Category" data-url="{{ route('categories.create') }}"
                style="background-color: hsl(237, 34%, 30%);"
                onmouseover="this.style.backgroundColor='hsl(237, 34%, 40%)'"
                onmouseout="this.style.backgroundColor='hsl(237, 34%, 30%)'">
                + Add Category
            </button>
        </div>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<!-- Categories Cards -->
<div class="row g-3">
    @foreach($categories as $category)
    <div class="col-md-3">
        <div class="card shadow-sm rounded-3 p-3 h-100 border-0 card-styles">
            <div class="d-flex align-items-start">
                <div class="bg-primary-subtle text-primary rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-tag" viewBox="0 0 16 16">
                        <path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0" />
                        <path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1m0 5.586 7 7L13.586 9l-7-7H2z" />
                    </svg>
                </div>

                <div class="flex-grow-1">
                    <h6 class="mb-1 fw-bold">{{ $category->name }}</h6>
                    <p class="mb-2 text-muted small">{{ $category->description }}</p>
                    <hr class="my-1" />
                    <small class="text-muted d-flex justify-content-between">
                        <span>Items in category</span>
                        <span class="fw-bold text-primary">
                            {{ $category->inventory_consumables_count + $category->inventory_non_consumables_count }}
                        </span>
                    </small>
                </div>

                <div class="ms-auto d-flex align-items-start gap-2">
                    <!-- Edit icon -->
                    <button type="button" title="Edit Item" class="btn p-0 border-0 bg-transparent text-gray edit" data-url="{{route('categories.edit', ['category' => $category->id])}}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen w-4 h-4">
                            <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                        </svg>
                    </button>

                    <!-- Delete icon -->
                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
                        @csrf
                        @method('DELETE')
                        <button type="button" title="Delete Item" class="btn p-0 border-0 bg-transparent text-danger delete mb-1" data-url="{{route('categories.destroy', ['category' => $category->id])}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 lucide-trash-2 w-4 h-4">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                <line x1="10" x2="10" y1="11" y2="17"></line>
                                <line x1="14" x2="14" y1="11" y2="17"></line>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card shadow-sm border-0 rounded-4 card-styles mt-20">
    <div class="card-body p-0">
        <div class="table-responsive rounded-4">
            <table class="table align-middle table-hover mb-0" id="categories_table">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted small">
                        <th>{{ __('#') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Created Date') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="categories-table-body" class="text-muted small">
                    {!!$categories_table!!}
                </tbody>
            </table>
            <div class="flex justify-center mb-3 ms-3">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="categories_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal content loaded dynamically -->
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div id="loading-spinner">
    <div class="spinner"></div>
</div>
@endsection