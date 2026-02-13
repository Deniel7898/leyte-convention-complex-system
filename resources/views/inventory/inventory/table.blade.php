@foreach($inventories as $inventory)
    <tr class="text-center">
        <td><p>{{ $loop->iteration }}</p></td>
        <td><p>{{ $inventory->item->name ?? '--' }}</p></td>
        <td><p>{{ $inventory->receive_date ?? '--' }}</p></td>
        <td>
           @if($inventory->item->type == 0)
                <span class="badge bg-danger text-white">Consumable</span>
            @else
                <span class="badge bg-success text-white">Non-Consumable</span>
            @endif
        </td>
        <td><p>{{ $inventory->qr_code->code ?? '--' }}</p></td>
        <td><p>{{ $inventory->warranty_expires ?? '--' }}</p></td>
        <td>
          <button class="btn btn-primary edit" 
                data-url="{{ route('inventory.edit', ['inventory' => $inventory->id]) }}">
            <i class="lni lni-pencil"></i>
        </button>
           <button class="btn btn-danger delete"
                data-url="{{ route('inventory.destroy', $inventory->id) }}">
                <i class="lni lni-trash-can"></i>
            </button>
        </td>             
    </tr>
@endforeach