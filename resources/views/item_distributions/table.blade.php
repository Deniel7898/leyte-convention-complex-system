@if($itemDistributions->count() > 0)
    @foreach($itemDistributions as $itemDistribution)
        <tr class="text-center">
            <td><p>{{ $loop->iteration }}</p></td>
            <td><p>{{ $itemDistribution->item->item->name ?? '--' }}</p></td>
            <td><p>{{ $itemDistribution->distribution_date ?? '--' }}</p></td>
            <td>
                <p>
                    @if($itemDistribution->type === 0)
                        Distribution
                    @elseif($itemDistribution->type === 1)
                        Borrow
                    @else
                        --
                    @endif
                </p>
            </td>
            <td><p>{{ $itemDistribution->qr_code ?? '--' }}</p></td>
            <td><p>{{ $itemDistribution->quantity ?? '--' }}</p></td>
            <td><p>{{ isset($itemDistribution->status) ? ucwords($itemDistribution->status) : '--' }}</p></td>
            <td><p>{{ $itemDistribution->description ?? '--' }}</p></td>
            <td><p>{{ $itemDistribution->due_date ?? '--' }}</p></td>
            <td><p>{{ $itemDistribution->returned_date ?? '--' }}</p></td>
            <td><p>{{ $itemDistribution->remarks ?? '--' }}</p></td>
            <td>
                <a href="" class="btn btn-warning"><i class="lni lni-eye"></i></a>
                <button class="btn btn-primary edit" data-url="{{ route('item_distributions.edit', $itemDistribution->id) }}"><i class="lni lni-pencil"></i></button>
                <button class="btn btn-danger delete" data-url="{{ route('item_distributions.destroy', $itemDistribution->id) }}"><i class="lni lni-trash-can"></i></button>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="12" class="text-center text-muted text-danger">{{ __('No Items found.') }}</td>
    </tr>
@endif