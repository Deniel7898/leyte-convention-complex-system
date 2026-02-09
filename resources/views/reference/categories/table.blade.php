@foreach($categories as $category)
    <tr>
        <td>{{ $category->id }}</td>
        <td>{{ $category->name }}</td>
        <td>{{ $category->description }}</td>
        <td>
            <button class="btn btn-sm btn-primary edit" data-url="{{ route('categories.edit', $category->id) }}">Edit</button>
            <button class="btn btn-sm btn-danger delete" data-url="{{ route('categories.destroy', $category->id) }}">Delete</button>
        </td>
    </tr>
@endforeach