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
<<<<<<< HEAD
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

<!-- Loading Spinner -->
<div id="loading-spinner">
    <div class="spinner"></div>
<<<<<<< HEAD
@endsection
=======
@endsection
>>>>>>> main-edit
