@extends('layouts.app')

@section('page_title', 'Reference Data')

@section('content')

<!-- ========== title-wrapper start ========== -->
<div class="title-wrapper pt-30">
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="title mb-30">
                <h2>{{ __('QR Codes') }}</h2>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
</div>
<!-- ========== title-wrapper end ========== -->

<div class="container">
    <div class="card-custom">

        <div class="card-header-custom">
            <span>QR Codes List</span>

            <form action="{{ route('qr_codes.store') }}" method="POST">
                @csrf
                <button type="submit" class="btn-add">
                    +
                </button>
            </form>
        </div>

        {{-- Success Message --}}
        @if(session(key: 'success'))
        <div id="success-alert" class="alert-success-custom">
            {{ session('success') }}
        </div>
        @endif


        {{-- Error Message --}}
        @if(session('error'))
        <div class="alert-danger-custom">
            {{ session('error') }}
        </div>
        @endif

        {{-- ✅ Wrap Table --}}
        <div id="qr_table">
            @include('reference.qr_code.table')
        </div>

    </div>
</div>
@endsection