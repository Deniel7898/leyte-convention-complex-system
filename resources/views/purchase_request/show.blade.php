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

                {{-- PRINT BUTTON (Only if Approved) --}}
                @if($purchaseRequest->status === 'approved')
                    <div style="margin-top:10px;">
                        <a href="{{ route('purchase_request.print', $purchaseRequest->id) }}"
                           target="_blank"
                           class="btn-modern btn-primary-modern">
                            Print
                        </a>
                    </div>
                @endif
            </div>

        </div>

        <div class="divider"></div>

        <table class="lcc-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="width:120px;">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseRequest->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>
@endsection
