@if($items->count() > 0)
@foreach($items as $item)
<tr class="text-start">
    <td>
        <p>{{ $loop->iteration }}</p>
    </td>
    <td>
        <p>{{ $item->name ?? '--' }}</p>
    </td>
    <td>
        <p>{{ $item->unit->name ?? '--' }}</p>
    </td>
    <td>
        <p>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tag" viewBox="0 0 16 16">
                <path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0" />
                <path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1m0 5.586 7 7L13.586 9l-7-7H2z" />
            </svg>
            {{ $item->category->name ?? '--' }}
        </p>
    </td>
    <td>
        <p>{{ $item->quantity ?? '--' }}</p>
    </td>
    <td>
        <p>{{ $item->remaining ?? '--' }}</p>
    </td>
    <td>
        @if($item->is_available)
        <span class="badge bg-success-subtle text-success">
            Available
        </span>
        @else
        <span class="badge bg-danger-subtle text-danger">
            Not Available
        </span>
        @endif
    </td>
    <td>
        <p>{{ $item->description ?? '--' }}</p>
    </td>

    <!-- Item Picture -->
    <td style="padding:0; margin:0; vertical-align:top; text-align:center">
        @if($item->picture)
        <img src="{{ asset('storage/' . $item->picture) }}"
            alt="{{ $item->name }}"
            width="50"
            class="clickable-img"
            style="cursor: pointer;"
            data-full="{{ asset('storage/' . $item->picture) }}">
        @else
        <span>No Image</span>
        @endif
    </td>

    <!-- Fullscreen Overlay -->
    <div id="imgLightbox" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
    background: rgba(0,0,0,0.8); justify-content:center; align-items:center; z-index:1050;">
        <button id="imgLightboxClose" style="position:absolute; top:20px; right:20px; background:none;
        border:none; color:white; font-size:1.5rem; cursor:pointer;">&times;</button>
        <img id="imgLightboxImg" src="" style="max-width:90%; max-height:90%; border-radius:8px;">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const clickableImgs = document.querySelectorAll('.clickable-img');
            const lightbox = document.getElementById('imgLightbox');
            const lightboxImg = document.getElementById('imgLightboxImg');
            const closeBtn = document.getElementById('imgLightboxClose');

            clickableImgs.forEach(img => {
                img.addEventListener('click', () => {
                    lightboxImg.src = img.dataset.full;
                    lightbox.style.display = 'flex';
                });
            });

            closeBtn.addEventListener('click', () => {
                lightbox.style.display = 'none';
                lightboxImg.src = '';
            });

            lightbox.addEventListener('click', (e) => {
                if (e.target === lightbox) {
                    lightbox.style.display = 'none';
                    lightboxImg.src = '';
                }
            });
        });
    </script>
    <!-- End Item Picture -->

    <td class="text-center">
        <a href="{{ route('viewItem.show', $item->id) }}"
            title="View Item"
            class="btn p-0 border-0 bg-transparent text-primary me-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye w-4 h-4">
                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        </a>
        <button type="button" title="Edit Item" class="btn p-0 border-0 bg-transparent text-gray me-2 edit" data-url="{{route('items.edit', ['item' => $item->id])}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen w-4 h-4">
                <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
            </svg>
        </button>
        <button type="button" title="Delete Item" class="btn p-0 border-0 bg-transparent text-danger delete" data-url="{{route('items.destroy', ['item' => $item->id])}}">
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
    <td colspan="12" class="text-center text-muted text-danger">{{ __('No Items found.') }}</td>
</tr>
@endif