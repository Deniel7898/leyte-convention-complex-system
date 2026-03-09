@if($service_records->count() > 0)
    @foreach($service_records as $service_record)
    <tr class="text-start">
        <td>{{ $loop->iteration }}</td>

        {{-- Item Name --}}
        <td>{{ $service_record->inventory->item->name ?? '--' }}</td>

        {{-- Category --}}
        <td>
            <i class="bi bi-tag me-1"></i>
            {{ $service_record->inventory->item->category->name ?? '--' }}
        </td>

        {{-- Service Type --}}
        <td>
            @if($service_record->type == 0)
                <span class="badge bg-warning-subtle text-orange">Maintenance</span>
            @else
                <span class="badge bg-primary-subtle text-primary">Installation</span>
            @endif
        </td>

        {{-- Quantity --}}
        <td>{{ $service_record->quantity ?? '--' }}</td>

        {{-- QR Code --}}
        <td>{{ $service_record->inventory->qrCode->code ?? '--' }}</td>

        {{-- Status --}}
        <td>
            @if($service_record->completed_date)
                <span class="badge bg-success-subtle text-success">Completed!</span>
            @else
                <span class="badge bg-warning-subtle text-orange">Pending!</span>
            @endif
        </td>

        {{-- Schedule Date --}}
        <td>
            {{ $service_record->schedule_date
                ? \Carbon\Carbon::parse($service_record->schedule_date)->format('M d, Y')
                : '--' }}
        </td>

        {{-- Description --}}
        <td>{{ $service_record->description ?? '--' }}</td>

        {{-- Person in Charge --}}
        <td>{{ $service_record->encharge_person ?? '--' }}</td>

        {{-- Service Picture --}}
        <td style="padding:0; margin:0; vertical-align:top; text-align:center">
            @if($service_record->picture)
                <img src="{{ asset('storage/' . $service_record->picture) }}"
                     alt="{{ $service_record->inventory->item->name ?? 'N/A' }}"
                     width="50"
                     class="clickable-img"
                     style="cursor: pointer;"
                     data-full="{{ asset('storage/' . $service_record->picture) }}">
            @else
                <span>No Image</span>
            @endif
        </td>

        {{-- Completed Date --}}
        <td>
            {{ $service_record->completed_date
                ? \Carbon\Carbon::parse($service_record->completed_date)->format('M d, Y')
                : '--' }}
        </td>

        {{-- Actions --}}
        <td class="text-center">
            <div class="d-flex justify-content-center align-items-center gap-2">

                {{-- Complete Button --}}
                @if(!$service_record->completed_date)
                <button type="button"
                    title="Complete Service"
                    class="btn p-0 border-0 bg-transparent text-success complete-service"
                    data-url="{{ route('service_records.complete', $service_record->id) }}"
                    data-item="{{ $service_record->inventory->item->name ?? 'N/A' }}"
                    data-type="{{ $service_record->type }}"
                    data-qr="{{ $service_record->inventory->qrCode->code ?? '' }}"
                    data-schedule="{{ \Carbon\Carbon::parse($service_record->schedule_date)->format('F d, Y') }}"
                    data-person="{{ $service_record->encharge_person }}">
                    <i class="bi bi-check-circle"></i>
                </button>
                @endif

                {{-- Dropdown Actions --}}
                <div class="dropdown">
                    <button class="btn p-0 border-0 bg-transparent text-gray"
                        type="button"
                        id="actionMenu{{ $service_record->id }}"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        title="Actions">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                        aria-labelledby="actionMenu{{ $service_record->id }}">

                        {{-- Undo Completion --}}
                        @if($service_record->completed_date)
                        <li>
                            <button type="button"
                                title="Undo Completion"
                                class="dropdown-item d-flex align-items-center text-warning undo-completion"
                                data-url="{{ route('service_records.undo', $service_record->id) }}"
                                data-item="{{ $service_record->inventory->item->name ?? 'N/A' }}"
                                data-qr="{{ $service_record->inventory->qrCode->code ?? '' }}"
                                data-schedule="{{ \Carbon\Carbon::parse($service_record->schedule_date)->format('F d, Y') }}"
                                data-person="{{ $service_record->encharge_person }}"
                                data-type="{{ $service_record->type }}">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>
                                Undo Complete
                            </button>
                        </li>
                        @endif

                        {{-- View --}}
                        <li>
                            <button type="button"
                                title="View Item"
                                class="dropdown-item d-flex align-items-center text-primary edit"
                                data-url="{{ route('service_records.show', $service_record->id) }}">
                                <i class="bi bi-eye me-2"></i>
                                View
                            </button>
                        </li>

                        {{-- Edit --}}
                        <li>
                            <button type="button"
                                title="Edit Item"
                                class="dropdown-item d-flex align-items-center text-gray edit"
                                data-url="{{ route('service_records.edit', $service_record->id) }}">
                                <i class="bi bi-pencil-square me-2"></i>
                                Edit
                            </button>
                        </li>

                        {{-- Delete --}}
                        <li>
                            <button type="button"
                                title="Delete Item"
                                class="dropdown-item d-flex align-items-center text-danger delete"
                                data-url="{{ route('service_records.destroy', ['service_record' => $service_record->id]) }}">
                                <i class="bi bi-trash2 me-2"></i>
                                Delete
                            </button>
                        </li>

                    </ul>
                </div>

            </div>
        </td>
    </tr>
    @endforeach
@else
<tr>
    <td colspan="12" class="text-center text-muted text-danger">{{ __('No Service Records found.') }}</td>
</tr>
@endif