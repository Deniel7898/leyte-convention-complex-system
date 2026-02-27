@if($units->count() > 0)
@foreach($units as $unit)
<tr>
    <td>{{ $units->firstItem() + $loop->index }}</td>
    <td><p>{{ $unit->name }}</p></td>
    <td><p>{{ $unit->description ?? '--'}}</p></td>
    <td class="text-center">
        <button type="button" title="Edit Item" class="btn p-0 border-0 bg-transparent text-gray me-2 edit" data-url="{{route('units.edit', ['unit' => $unit->id])}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen w-4 h-4">
                <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
            </svg>
        </button>
        <button type="button" title="Delete Item" class="btn p-0 border-0 bg-transparent text-danger delete" data-url="{{route('units.destroy', ['unit' => $unit->id])}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 lucide-trash-2 w-4 h-4">
                <path d="M3 6h18"></path>
                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                <line x1="10" x2="10" y1="11" y2="17"></line>
                <line x1="14" x2="14" y1="11" y2="17"></line>
            </svg>
        </button>
    </td>
</tr>
@endforeach
@else
<tr>
    <td colspan="12" class="text-center text-muted text-danger">{{ __('No Units found.') }}</td>
</tr>
@endif