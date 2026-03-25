@forelse($item->inventories as $unit)
@php
$status = strtolower($unit->status ?? 'available');
$badgeClass = match($status) {
'available' => 'bg-success-subtle text-success',
// Distribution
'borrowed' => 'bg-warning-subtle text-orange',
'issued' => 'bg-primary-subtle text-primary',
// Service
'maintenance' => 'bg-warning-subtle text-orange',
'installation' => 'bg-primary-subtle text-primary',
'inspection' => 'bg-info-subtle text-secondary',
'under repair' => 'bg-warning-subtle text-orange',
default => 'bg-secondary-subtle text-secondary',
};
@endphp

<tr>
    <td>{{ $loop->iteration }}</td>
    <td style="padding:0; margin:0; vertical-align:top; text-align:center">
        @if($unit->qrCode)
        <img src="{{ asset('storage/' . $unit->qrCode->qr_picture) }}"
            alt="{{ $unit->qrCode->code }}"
            width="40"
            class="clickable-image"
            style="cursor:pointer;"
            data-full="{{ asset('storage/' . $unit->qrCode->qr_picture) }}">
        <br>
        <small>{{ $unit->qrCode->code }}</small>
        @else
        QR
        @endif
    </td>
    <td>{{ $unit->holder ?? '--' }}</td>
    <td>{{ $unit->date_assigned ? \Carbon\Carbon::parse($unit->date_assigned)->format('M d, Y') : '--' }}</td>
    <td>{{ $unit->due_date ? \Carbon\Carbon::parse($unit->due_date)->format('M d, Y') : '--' }}</td>    
    <td><span class="badge {{ $badgeClass }}">{{ ucfirst($unit->status ?? 'Available') }}</span></td>
    <td>{{ $unit->notes ?? '-' }}</td>
    <td class="text-center">

        @if($status === 'available')
        <!-- Issue/Borrow Button -->
        <button type="button" title="Issue/Borrow Item"
            class="btn p-0 border-0 bg-transparent text-success me-2 add-itemDistribution"
            data-url="{{ route('item_distributions.create', $unit->id) }}"
            data-item-id="{{ $item->id }}"
            data-type="borrowed"
            data-inventory-id="{{ $unit->id }}"
            data-quick="1">
            <i class="bi bi-send"></i>
        </button>

        <!-- Service Button -->
        <button type="button" title="Service Item"
            class="btn p-0 border-0 bg-transparent text-orange me-2 add-service"
            data-url="{{ route('service_records.create') }}"
            data-item-id="{{ $item->id }}"
            data-inventory-id="{{ $unit->id }}"
            data-quick="1">
            <i class="bi bi-wrench"></i>
        </button>
        @endif

        @php
        $status = strtolower($unit->status ?? 'available');

        // Latest service record
        $service_record = $unit->serviceRecords()->latest()->first();

        // Latest distribution using the correct relationship
        $item_distribution = $unit->itemDistributions()->latest()->first();
        @endphp

        {{-- Return Button --}}
        @if(in_array($status, ['issued', 'borrowed']) && $item_distribution)
        <button type="button" title="Return Item"
            class="btn p-0 border-0 bg-transparent text-success me-2 show-return"
            data-url="{{ route('item_distributions.return_form', $item_distribution->id) }}"
            data-unit-id="{{ $unit->id }}">
            <i class="bi bi-arrow-repeat"></i>
        </button>
        @endif

        {{-- Complete Service Button --}}
        @if($service_record && in_array($status, ['maintenance', 'installation', 'inspection']))
        <button type="button"
            title="Complete Service"
            class="btn p-0 border-0 bg-transparent text-success me-2 complete-service"
            data-url="{{ route('service_records.show_service', $service_record->id) }}"
            data-unit-id="{{ $unit->id }}">
            <i class="bi bi-check-circle"></i>
        </button>
        @endif

        <!-- 3-dot Dropdown -->
        <div class="dropdown d-inline">
            <button class="btn p-0 border-0 bg-transparent text-gray" type="button" id="actionMenu{{ $item->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenu{{ $item->id }}">
                <!-- View Item -->
                <li>
                    <a class="dropdown-item edit text-primary" href="javascript:void(0)"
                        data-url="{{ route('inventory.show', $unit->id) }}" data-item-id="{{ $item->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye w-4 h-4 me-2">
                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        View
                    </a>
                </li>

                <!-- Edit Item -->
                <li>
                    <a class="dropdown-item edit-non-consumable text-grey" href="javascript:void(0)"
                        data-url="{{ route('items.edit', $unit->id) }}" data-item-id="{{ $item->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen w-4 h-4 me-2">
                            <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                        </svg>
                        Edit
                    </a>
                </li>

                <!-- Delete Item -->
                <li>
                    <a class="dropdown-item text-danger delete" href="javascript:void(0)"
                        data-url="{{ route('inventory.destroy', $unit->id) }}" data-item-id="{{ $item->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 w-4 h-4 me-2">
                            <path d="M3 6h18"></path>
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            <line x1="10" x2="10" y1="11" y2="17"></line>
                            <line x1="14" x2="14" y1="11" y2="17"></line>
                        </svg>
                        Delete
                    </a>
                </li>
            </ul>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center py-3">No units found.</td>
</tr>
@endforelse