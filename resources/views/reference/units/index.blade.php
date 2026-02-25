@extends('layouts.app')

@section('page_title', 'Reference Data')

@section('content')
<!-- ========== title-wrapper start ========== -->
<div class="title-wrapper pt-30">
    <div class="row align-items-center">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Left: Page Title -->
                <div class="title">
                    <div>
                        <h2>Units List</h2>
                        <p class="text-muted mb-0 text-sm">This is the reference data used for item measurements in inventory.</p>
                    </div>
                </div>

                <!-- Right: Add Button -->
                <button class="btn text-white add-unit"
                    title="Add Unit" data-url="{{ route('units.create') }}"
                    style="background-color: hsl(237, 34%, 30%);" onmouseover="this.style.backgroundColor='hsl(237, 34%, 40%)'" onmouseout="this.style.backgroundColor='hsl(237, 34%, 30%)'">
                    + Add Unit     
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<div class="card shadow-sm border-0 rounded-4 card-styles mt-2">
    <div class="card-body p-0">
        <div class="table-responsive rounded-4">
            <table class="table align-middle table-hover" id="units_table">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted small">
                        <th>{{ __('#') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="units-table-body" class="text-muted small">
                    {!!$units_table!!}
                </tbody>
            </table>
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