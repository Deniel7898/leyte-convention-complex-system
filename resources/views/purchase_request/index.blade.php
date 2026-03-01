@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="lcc-card">

        <h3 class="page-title">Purchase Requests</h3>

        <div class="top-bar">

        <div class="left-actions">
<a href="{{ route('purchase_request.create') }}"
            class="btn-modern btn-primary-modern">
                Create Request
            </a>

            <a href="{{ route('purchase_request.printApproved') }}"
            target="_blank"
            class="btn-modern btn-primary-modern">
                Print Approved Items
            </a>
        </div>

        <div class="right-actions">
            <form method="GET"
                action="{{ route('purchase_request.index') }}"
                class="search-form">

                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search..."
                    class="search-input">

                <button type="submit"
                        class="btn-modern btn-info-modern">
                    Search
                </button>

            </form>
        </div>

    </div>

    <div>

        @if(session('success'))
        <div id="success-alert" class="lcc-success mt-3">
            {{ session('success') }}
        </div>
        @endif

        @if($requests->count())
        @include('purchase_request.table')

        <div class="pagination-wrapper">
            {{ $requests->withQueryString()->links() }}
        </div>
        @else
        <div class="empty-state">No purchase requests found.</div>
        @endif

    </div>
</div>
@endsection
