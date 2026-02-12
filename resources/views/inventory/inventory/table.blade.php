@foreach($inventories as $inventory)
    <tr class="text-center">
        <td><p>{{ $loop->iteration }}</p></td>
        <td><p>{{ $inventory->item->name ?? '--' }}</p></td>
        <td><p>{{ $inventory->receive_date ?? '--' }}</p></td>
        <td><p>{{ $inventory->qr_code->code ?? '--' }}</p></td>
        <td><p>{{ $inventory->item->description ?? '--' }}</p></td>
        <td><p>{{ $inventory->warranty_expires ?? '--' }}</p></td>
        <td>
            <button class="btn btn-primary edit" data-url="">
                <i class="lni lni-pencil"></i>
            </button>
            <button class="btn btn-danger delete" data-url="">
                <i class="lni lni-trash-can"></i>
            </button>
        </td>             
    </tr>
@endforeach