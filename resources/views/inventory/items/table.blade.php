@foreach($items as $item)
    <tr class="text-center">
        <td><p>{{ $loop->iteration }}</p></td>
        <td><p>{{ $item->name ?? '--' }}</p></td>
         <td>
            @if($item->type == 0)
                <span class="badge bg-danger text-white">Consumable</span>
            @else
                <span class="badge bg-success text-white">Non-Consumable</span>
            @endif
        </td>
        <td><p>{{ $item->quantity ?? '--' }}</p></td>
        <td><p>{{ $item->remaining ?? '--' }}</p></td>
        <td><p>{{ $item->unit->name ?? '--' }}</p></td>
        <td>
            <p>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tag" viewBox="0 0 16 16">
                    <path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0"/>
                    <path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1m0 5.586 7 7L13.586 9l-7-7H2z"/>
                </svg>
                {{ $item->category->name ?? '--' }}
            </p>
        </td>
        <td>
            @if($item->availability == 1)
                <span class="badge bg-success text-white">Available</span>
            @else
                <span class="badge bg-danger text-white">Not Available</span>
            @endif
        </td>
        <td><p>{{ $item->description ?? '--' }}</p></td>
        <td><p>{{ $item->picture ?? '--' }}</p></td>
        <td>
            <a href="#" class="btn btn-warning">
                <i class="lni lni-eye"></i>
            </a>
            <button class="btn btn-primary edit" data-url="{{route('items.edit', ['item' => $item->id])}}"><i class="lni lni-pencil"></i></button>
            <button class="btn btn-danger delete" data-url="{{route('items.destroy', ['item' => $item->id])}}"><i class="lni lni-trash-can"></i></button>
        </td>
    </tr>
@endforeach