@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="lcc-card">

        <h3 class="page-title">Create Purchase Request</h3>

        <form action="{{ route('purchase_request.store') }}" method="POST" class="modern-form">
            @csrf

            <div class="form-group">
                <label class="form-label">Request Date</label>
                <input type="date"
                       name="request_date"
                       class="modern-input"
                       required>
            </div>

            <h5 class="section-title">Items</h5>

            <div id="items-wrapper">
                <div class="item-row">
                    <input type="text"
                           name="items[0][description]"
                           class="modern-input"
                           placeholder="Item Description"
                           required>

                    <input type="number"
                           name="items[0][quantity]"
                           class="modern-input"
                           placeholder="Quantity"
                           required>
                </div>
            </div>

            <button type="submit"
              class="btn-modern btn-primary-modern">
              Submit Request
            </button>
        </form>
    </div>
</div>
@endsection
