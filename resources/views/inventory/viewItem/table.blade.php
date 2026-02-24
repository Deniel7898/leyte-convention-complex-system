@if($viewItems->count() > 0)
    @foreach($viewItems as $key => $viewItem)
    <tr class="text-center">
        <td>{{ $key + 1 }}</td>
        <td>{{ $viewItem->item->name ?? '--' }}</td>
        <td><p>{{ $viewItem->received_date ?? '--' }}</p></td>
        <td>
            @if(($viewItem->item?->type ?? 0) == 0)
            <span class="badge bg-secondary text-white">Consumable</span>
            @else
            <span class="badge bg-primary text-white">Non-Consumable</span>
            @endif
        </td>
        <td><p>{{ $viewItem->item->unit->name ?? '--' }}</p></td>
        <td>
            <p>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tag" viewBox="0 0 16 16">
                    <path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0" />
                    <path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1m0 5.586 7 7L13.586 9l-7-7H2z" />
                </svg>
                {{ $viewItem->item->category->name ?? '--' }}
            </p>
        </td>
        <td><p>{{ $viewItem->itemDistributions->last()?->status ? ucfirst($viewItem->itemDistributions->last()->status) : 'Available' }}</p></td>
        <td><p>{{ $viewItem->qr_code->code ?? '--' }}</p></td>
        <td>{{ $viewItem->warranty_expires ?? '--' }}</td>
        <td>{{ $viewItem->item->description ?? '--' }}</td>
        <td>
            <button class="btn btn-primary edit" data-url="{{ route('viewItem.edit', $viewItem->id) }}"> <i class="lni lni-pencil"></i></button>
            <button class="btn btn-danger delete" data-url="{{ route('viewItem.destroy', $viewItem->id) }}"> <i class="lni lni-trash-can"></i></button>
        </td>
    </tr>
    @endforeach
@else
    <tr>
        <td colspan="12" class="text-center text-muted text-danger">{{ __('No Items found.') }}</td>
    </tr>
@endif