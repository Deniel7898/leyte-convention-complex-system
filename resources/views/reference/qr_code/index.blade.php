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
        @if(session('success'))
            <div class="alert-success-custom">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Message --}}
        @if(session('error'))
            <div class="alert-danger-custom">
                {{ session('error') }}
            </div>
        @endif

        {{-- TABLE INCLUDE (FIXED PATH) --}}
        @include('reference.qr_code.table')

    </div>
</div>
@endsection
