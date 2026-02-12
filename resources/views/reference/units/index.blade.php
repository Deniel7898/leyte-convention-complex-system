@extends('layouts.app')

@section('page_title', 'Reference Data')

@section('content')
<!-- ========== title-wrapper start ========== -->
<div class="title-wrapper pt-30">
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="title mb-30">
                <h2>{{ __('Units') }}</h2>
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
            <h6 class="mb-25">{{ __('Units List') }}</h6>
            <div class="table-responsive">
                <button class="btn btn-primary mb-3 add-unit" data-url="{{route('units.create')}}">
                    <i class="lni lni-plus"></i>
                </button>
                <table class="table" id="units_table">
                    <thead>
                    <tr class="text-center">
                        <th><h6>{{ __('#') }}</h6></th>
                        <th><h6>{{ __('Name') }}</h6></th>
                        <th><h6>{{ __('Description') }}</h6></th>
                        <th><h6>{{ __('Actions') }}</h6></th>
                    </tr>
                    </thead>
                    <tbody>
                    {!!$units_table!!}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="units_modal" tabindex="-1" aria-hidden="true">
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
