@if($itemDistributions->count() > 0)
@foreach($itemDistributions as $itemDistribution)
<tr class="text-start">
    <td>
        <p>{{ $loop->iteration }}</p>
    </td>
    <td>
        <p>{{ $itemDistribution->item->item->name ?? '--' }}</p>
    </td>
    <td>
        @if(($itemDistribution->item->item?->type ?? 0) == 0)
        <span class="badge bg-success-subtle text-success">Consumable</span>
        @else
        <span class="badge bg-primary-subtle text-primary">Non-Consumable</span>
        @endif
    </td>
    <td>
        <p>{{ $itemDistribution->distribution_date ?? '--' }}</p>
    </td>
    <td>
        @php
        $type = $itemDistribution->type;
        $typeClasses = [
        0 => 'bg-primary-subtle text-primary',
        1 => 'bg-warning-subtle text-warning', ];

        $class = $typeClasses[$type] ?? 'bg-secondary-subtle text-secondary';
        $label = $type === 0 ? 'Distribution' : ($type === 1 ? 'Borrow' : '--');
        @endphp

        <span class="badge {{ $class }}">
            {{ $label }}
        </span>
    </td>
    <td>
        <p>{{ $itemDistribution->qr_code ?? '--' }}</p>
    </td>
    <td>
        <p>{{ $itemDistribution->quantity ?? '--' }}</p>
    </td>
    <td>
        @php
        $status = $itemDistribution->status ?? 'available';

        // Map statuses to badge classes
        $statusClasses = [
        'distributed' => 'bg-primary-subtle text-primary',
        'borrowed' => 'bg-warning-subtle text-warning',
        'partial' => 'bg-warning-subtle text-warning',
        'returned' => 'bg-info-subtle text-info',
        'received' => 'bg-success-subtle text-success',
        'pending' => 'bg-secondary-subtle text-secondary',
        'available' => 'bg-success-subtle text-success',
        ];

        // Get class for current status, fallback to secondary if unknown
        $class = $statusClasses[strtolower($status)] ?? 'bg-secondary-subtle text-secondary';
        @endphp

        <span class="badge {{ $class }}">
            {{ ucwords($status) }}
        </span>
    </td>
    <td>
        <p>{{ $itemDistribution->description ?? '--' }}</p>
    </td>
    <td>
        <p>{{ $itemDistribution->due_date ?? '--' }}</p>
    </td>
    <!-- <td>
        <p>{{ $itemDistribution->returned_date ?? '--' }}</p>
    </td> -->
    <td>
        <p>{{ $itemDistribution->remarks ?? '--' }}</p>
    </td>
    <td class="text-center">
        <button type="button" title="View Item" class="btn p-0 border-0 bg-transparent text-primary me-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye w-4 h-4">
                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        </button>
        <button type="button" title="Edit Item" class="btn p-0 border-0 bg-transparent text-gray me-2 edit" data-url="{{ route('item_distributions.edit', $itemDistribution->id) }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen w-4 h-4">
                <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
            </svg>
        </button>
        <button type="button" title="Delete Item" class="btn p-0 border-0 bg-transparent text-danger delete" data-url="{{ route('item_distributions.destroy', $itemDistribution->id) }}">
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