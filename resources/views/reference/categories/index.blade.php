@extends('layouts.app')

@section('page_title', 'Reference Data')

@section('content')
<!-- ========== title-wrapper start ========== -->
<div class="title-wrapper pt-30">
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="title mb-30">
                <h2>{{ __('Categories') }}</h2>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
</div>
<!-- ========== title-wrapper end ========== -->

<div class="card-styles">
    <div class="card-style-3 mb-30">
        <div class="card-content">
            <h6 class="mb-25">{{ __('Categories List') }}</h6>
            <div class="table-responsive">
                <button class="btn btn-primary mb-3 add-category" data-url="{{route('categories.create')}}">
                    <i class="lni lni-plus"></i>
                </button>
                <table class="table" id="categories_table">
                    <thead>
                        <tr>
                            <th class="align-middle">ID</th>
                            <th class="align-middle">Name</th>
                            <th class="align-middle">Description</th>
                            <th class="text-center align-middle">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    {!!$categories_table!!}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="categories_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            
        </div>
    </div>
</div>
<!-- Loading Spinner -->
<div id="loading-spinner">
  <div class="spinner"></div>
</div>
@endsection