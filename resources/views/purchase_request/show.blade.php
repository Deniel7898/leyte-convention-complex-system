@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="lcc-card">

        <div class="details-header" style="display:flex; justify-content:space-between; align-items:center;">
            
            <div>
                <h3 class="page-title">
                    Purchase Request #{{ $purchaseRequest->id }}
                </h3>

                <p class="details-date">
                    {{ \Carbon\Carbon::parse($purchaseRequest->request_date)->format('F d, Y') }}
                </p>

                <p class="details-date">
                    Created by: {{ $purchaseRequest->creator->name ?? 'Unknown' }}
                </p>
            </div>

            <div style="text-align:right;">
                <span class="badge-status status-{{ $purchaseRequest->status }}">
                    {{ ucfirst($purchaseRequest->status) }}
                </span>
            </div>

        </div>

        <div class="divider"></div>

        <div class="qr-card">
            @forelse($purchaseRequest->items as $item)
                <div class="qr-block">
                    <strong>{{ $item->description }}</strong>
                    <span>{{ $item->quantity }}</span>
                </div>
            @empty
                <div class="empty-state">No items found.</div>
            @endforelse
        </div>
        </div>
    </div>

</div>
@endsection
