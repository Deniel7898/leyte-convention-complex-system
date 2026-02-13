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
        <td>
            @if($item->availability == 1)
                <span class="badge bg-success text-white">Available</span>
            @else
                <span class="badge bg-danger text-white">Not Available</span>
            @endif
        </td>
        <td><p>{{ $item->quantity ?? '--' }}</p></td>
        <td><p>{{ $item->remaining ?? '--' }}</p></td>
        <td><p>{{ $item->unit->name ?? '--' }}</p></td>
        <td><p>{{ $item->category->name ?? '--' }}</p></td>
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