@foreach($categories as $category)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $category->name }}</td>
    <td>{{ $category->description }}</td>
    <td class="text-center">
        <button class="btn btn-primary edit" data-url="{{route('categories.edit', ['category' => $category->id])}}"><i class="lni lni-pencil"></i></button>
        <button class="btn btn-danger delete" data-url="{{route('categories.destroy', ['category' => $category->id])}}"><i class="lni lni-trash-can"></i></button>
    </td>
</tr>
@endforeach