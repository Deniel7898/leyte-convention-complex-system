@foreach($units as $unit)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $unit->name }}</td>
    <td>{{ $unit->description }}</td>
    <td class="text-center">

        <!-- Edit Icon -->
        <button class="btn btn-sm btn-link text-warning edit"
                data-url="{{ route('units.edit', $unit->id) }}">
            <i class="bi bi-pencil-square fs-5"></i>
        </button>

        <!-- Delete Icon -->
        <button class="btn btn-sm btn-link text-danger delete"
                data-url="{{ route('units.destroy', $unit->id) }}">
            <i class="bi bi-trash fs-5"></i>
        </button>

    </td>
</tr>
@endforeach
