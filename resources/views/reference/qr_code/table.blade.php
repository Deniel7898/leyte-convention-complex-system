@if($qrCodes->count() > 0)
@foreach($qrCodes as $qrCode)
<tr>
    <td>
        <p>{{ $loop->iteration }}</p>
    </td>
    <td>{{ $qrCode->item->item->name ?? '--' }}</td>
    <td>
        @if($qrCode->item->item->type == 0)
        <span class="badge bg-success-subtle text-success">Consumable</span>
        @else
        <span class="badge bg-primary-subtle text-primary">Non-Consumable</span>
        @endif
    </td>
    <td>{{ $qrCode->item->item->unit->name ?? '--' }}</td>
    <td>{{ $qrCode->item->item->category->name ?? '--' }}</td>
    <td>
        <span class="badge bg-success-subtle text-success">
            {{ $qrCode->code ?? '--' }}
        </span>
    </td>
    <td>{{ $qrCode->picture ?? '--' }}</td>
    <td>
        @php
        // Use the QR code status if it exists, otherwise default to 'available'
        $status = $qrCode->status ?? 'available';

        // Map status to CSS classes
        $statusClasses = [
        'available' => 'bg-success-subtle text-success',
        'used' => 'bg-primary-subtle text-primary',
        'expired' => 'bg-danger-subtle text-danger',
        ];

        // Pick the class based on status, default to gray
        $class = $statusClasses[strtolower($status)] ?? 'bg-success-subtle text-success';
        @endphp

        <span class="badge {{ $class }}">
            {{ ucfirst($status) }}
        </span>
    </td>
    <td>{{ $qrCode->created_at?->format('M d, Y') ?? '--' }}</td>
    <td>{{ $qrCode->user->name ?? '--' }}</td>
    <td class="text-center">
        <button type="button" title="View Item" class="btn p-0 border-0 bg-transparent text-primary me-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye w-4 h-4">
                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        </button>
    </td>
</tr>
@endforeach
@else
<tr>
    <td colspan="12" class="text-center text-muted text-danger">{{ __('No QR Codes found.') }}</td>
</tr>
@endif