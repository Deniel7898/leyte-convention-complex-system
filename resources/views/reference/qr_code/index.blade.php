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
                        <h2>QR Codes List</h2>
                        <p class="text-muted mb-0 text-sm">Manage and view all QR codes generated for inventory items.</p>
                    </div>
                </div>

                <!-- Right: Add Button -->
                <button class="btn text-white add-qrCode"
                    title="Generate QR Code" data-url="{{ route('qr_codes.create') }}"
                    style="background-color: hsl(237, 34%, 30%);" onmouseover="this.style.backgroundColor='hsl(237, 34%, 40%)'" onmouseout="this.style.backgroundColor='hsl(237, 34%, 30%)'">
                    + Generate QR Code
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<div class="card shadow-sm border-0 rounded-4 card-styles mt-2">
    <div class="card-body p-0">
        <div class="table-responsive rounded-4">
            <table class="table align-middle table-hover" id="qrCodes_table">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted small">
                        <th>{{ __('#') }}</th>
                        <th>{{ __('Item Name') }}</th>
                        <th>{{ __('QR Codes') }}</th>
                        <th>{{ __('Qr Picture') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Generated At') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="qrCodes-table-body" class="text-muted small">
                    {!!$qrCodes_table!!}
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="qrCodes_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>
@endsection