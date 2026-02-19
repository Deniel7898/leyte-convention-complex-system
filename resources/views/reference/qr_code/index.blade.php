@extends('layouts.app')

@section('content')
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
