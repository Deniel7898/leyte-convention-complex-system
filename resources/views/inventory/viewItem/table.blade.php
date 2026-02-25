@if($viewItems->count() > 0)
@foreach($viewItems as $key => $viewItem)
<tr class="text-start">
    <td>{{ $key + 1 }}</td>
    <td>{{ $viewItem->item->name ?? '--' }}</td>
    <td>
        {{ $viewItem->received_date && $viewItem->received_date != '--'
            ? \Carbon\Carbon::parse($viewItem->received_date)->format('M d, Y')
                : '--' }}
    </td>
    <td>
        @if(($viewItem->item?->type ?? 0) == 0)
        <span class="badge bg-success-subtle text-success">Consumable</span>
        @else
        <span class="badge bg-primary-subtle text-primary">Non-Consumable</span>
        @endif
    </td>
    <td>
        <p>{{ $viewItem->item->unit->name ?? '--' }}</p>
    </td>
    <td>
        <p>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tag" viewBox="0 0 16 16">
                <path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0" />
                <path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1m0 5.586 7 7L13.586 9l-7-7H2z" />
            </svg>
            {{ $viewItem->item->category->name ?? '--' }}
        </p>
    </td>
    <td>
        @php
        $status = $viewItem->itemDistributions->last()?->status ?? 'available';

        $statusClasses = [
        'distributed' => 'bg-primary-subtle text-primary',
        'borrowed' => 'bg-warning-subtle text-warning',
        'partial' => 'bg-warning-subtle text-warning',
        'returned' => 'bg-info-subtle text-info',
        'received' => 'bg-success-subtle text-success',
        'pending' => 'bg-secondary-subtle text-secondary',
        'available' => 'bg-success-subtle text-success',
        ];

        $class = $statusClasses[strtolower($status)] ?? 'bg-secondary-subtle text-secondary';
        @endphp

        <span class="badge {{ $class }}">
            {{ ucfirst($status) }}
        </span>
    </td>
    <td>
        <p>{{ $viewItem->qr_code->code ?? '--' }}</p>
    </td>
    <td>
        <p>
            {{ $viewItem->warranty_expires && $viewItem->warranty_expires != '--'
            ? \Carbon\Carbon::parse($viewItem->warranty_expires)->format('M d, Y')
            : '--' }}
        </p>
    </td>
    <td>{{ $viewItem->item->description ?? '--' }}</td>
    <td class="text-center">
        <button type="button" title="Edit Item" class="btn p-0 border-0 bg-transparent text-gray me-2 edit" data-url="{{ route('viewItem.edit', $viewItem->id) }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen w-4 h-4">
                <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
            </svg>
        </button>
        <button type="button" title="Delete Item" class="btn p-0 border-0 bg-transparent text-danger delete" data-url="{{ route('viewItem.destroy', $viewItem->id) }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 lucide-trash-2 w-4 h-4">
                <path d="M3 6h18"></path>
                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                <line x1="10" x2="10" y1="11" y2="17"></line>
                <line x1="14" x2="14" y1="11" y2="17"></line>
            </svg>
        </button>
    </td>
</tr>
@endforeach
@else
<tr>
    <td colspan="12" class="text-center text-muted text-danger">{{ __('No Items found.') }}</td>
</tr>
@endif