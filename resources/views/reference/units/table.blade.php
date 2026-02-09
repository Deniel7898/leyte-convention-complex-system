@foreach($units as $unit)
    <tr class="text-center">
        <td><p>{{ $loop->iteration }}</p></td>
        <td><p>{{ $unit->name }}</p></td>
        <td><p>{{ $unit->description ?? '--'}}</p></td>
        <td>
            
            <button class="btn btn-primary edit" data-url="{{route('units.edit', ['unit' => $unit->id])}}"><i class="lni lni-pencil"></i></button>
            <button class="btn btn-danger delete" data-url="{{route('units.destroy', ['unit' => $unit->id])}}"><i class="lni lni-trash-can"></i></button>
    </tr>
@endforeach