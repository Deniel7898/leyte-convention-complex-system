@if(isset($purchaseRequests) && $purchaseRequests->count() > 0)

    @foreach($purchaseRequests as $key => $purchaseRequest)
        <tr data-id="{{ $purchaseRequest->id }}">
            <td>{{ $purchaseRequests->firstItem() + $key }}</td>
            <td>{{ optional($purchaseRequest->request_date)->format('M d, Y') ?: '-' }}</td>
            <td>{{ count($purchaseRequest->items ?? []) }}</td>
            <td>{{ $purchaseRequest->creator->name ?? 'N/A' }}</td>
            <td>{{ $purchaseRequest->created_at->format('M d, Y') }}</td>
            <td class="text-center">
                <div class="action-icons">
                    <button class="btn btn-sm btn-action edit-purchase-request"
                            data-id="{{ $purchaseRequest->id }}"
                            data-url="{{ route('purchase-requests.edit', $purchaseRequest->id) }}"
                            title="Edit">
                        <i class="bi bi-pencil-square"></i>
                    </button>

                    <a href="{{ route('purchase-requests.print', $purchaseRequest->id) }}"
                       class="btn btn-sm btn-action"
                       title="Print"
                       target="_blank">
                        <i class="bi bi-printer"></i>
                    </a>

                    <button class="btn btn-sm btn-action delete-purchase-request"
                            data-id="{{ $purchaseRequest->id }}"
                            data-url="{{ route('purchase-requests.destroy', $purchaseRequest->id) }}"
                            title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach

@else

    <tr>
        <td colspan="6" class="text-center py-4">
            <p class="pr-text-muted">
                No purchase requests found.
                <a href="{{ route('purchase-requests.create') }}" class="text-decoration-none">
                    Create one now
                </a>
            </p>
        </td>
    </tr>

@endif