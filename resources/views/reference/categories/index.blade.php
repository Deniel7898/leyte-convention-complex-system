@extends('layouts.app')

@section('page_title', 'Reference Data')

@section('content')
<div class="card mb-3 shadow-sm border-0 card-styles mt-30">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center p-1">
            <div class="title">
                <h3 class="mb-1 fw-700">Categories List</h3>
                <p class="text-muted mb-0 text-sm">This is the reference data used for inventory and items.</p>
            </div>

            <button class="btn text-white add-category"
                title="Add Category" data-url="{{ route('categories.create') }}"
                style="background-color: hsl(237, 34%, 30%)"
                onmouseover="this.style.backgroundColor='hsl(237, 34%, 40%)'"
                onmouseout="this.style.backgroundColor='hsl(237, 34%, 30%)'">
                + Add Category
            </button>
        </div>
    </div>
</div>

<!-- Categories Cards -->
<div id="categories_cards">
    @include('reference.categories.cards', ['categories' => $categories])
</div>

<!-- Modal -->
<div class="modal fade" id="categories_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<!-- Loading Spinner -->
<div id="loading-spinner">
    <div class="spinner"></div>
</div>
@endsection