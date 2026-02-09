@foreach($categories as $category)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $category->name }}</td>
    <td>{{ $category->description }}</td>
    <td class="text-center">

        <!-- Edit Icon -->
        <button class="btn btn-sm btn-link text-warning edit"
                data-url="{{ route('categories.edit', $category->id) }}">
            <i class="bi bi-pencil-square fs-5"></i>
        </button>

        <!-- Delete Icon -->
        <button class="btn btn-sm btn-link text-danger delete"
                data-url="{{ route('categories.destroy', $category->id) }}">
            <i class="bi bi-trash fs-5"></i>
        </button>

    </td>
</tr>
@endforeach
