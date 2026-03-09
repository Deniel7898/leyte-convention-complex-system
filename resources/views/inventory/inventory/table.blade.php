 @if($inventories->count() > 0)
 @foreach($inventories as $inventory)
 <tr class="text-start">
     <td>{{ $inventories->firstItem() + $loop->index }}</td>
     <td>{{ $inventory->item->name ?? '--' }}</td>
     <td>{{ $inventory->item->unit->name ?? '--' }}</td>
     <td>
         <i class="bi bi-tag me-1"></i>
         {{ $inventory->item->category->name ?? '--' }}
     </td>
     <td>
         {{ $inventory->received_date && $inventory->received_date != '--'
            ? \Carbon\Carbon::parse($inventory->received_date)->format('M d, Y')
                : '--' }}
     </td>
     <td>
         @php
         $status = $inventory->itemDistributions->last()?->status ?? 'available';

         $statusClasses = [
         'distributed' => 'bg-primary-subtle text-primary',
         'borrowed' => 'bg-warning-subtle text-orange',
         'partial' => 'bg-warning-subtle text-orange',
         'returned' => 'bg-info-subtle text-info',
         'pending' => 'bg-secondary-subtle text-secondary',
         'available' => 'bg-success-subtle text-success',
         ];

         $class = $statusClasses[strtolower($status)] ?? 'bg-secondary-subtle text-secondary';
         @endphp

         <span class="badge {{ $class }}">
             {{ ucfirst($status) }}
         </span>
     </td>
     <td>{{ $inventory->qrCode->code ?? '--' }}</td>

     <!-- Qr Picture clickable -->
     <td style="padding:0; margin:0; vertical-align:top; text-align:center">
         @if($inventory->qrCode?->qr_picture)
         <img src="{{ asset('storage/' . $inventory->qrCode->qr_picture) }}"
             alt="{{ $inventory->qrCode->item->name ?? 'QR Code' }} QR Code"
             width="60"
             class="clickable-img"
             style="cursor: pointer;"
             data-full="{{ asset('storage/' . $inventory->qrCode->qr_picture) }}">
         @else
         --
         @endif
     </td>

     <!-- Fullscreen Overlay -->
     <div id="qrLightbox" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
    background: rgba(0,0,0,0.8); justify-content:center; align-items:center; z-index:1050;">
         <button id="qrLightboxClose" style="position:absolute; top:20px; right:20px; background:none; 
        border:none; color:white; font-size:1.5rem; cursor:pointer;">&times;</button>
         <img id="qrLightboxImg" src="" style="max-width:90%; max-height:90%; border-radius:8px;">
     </div>

     <script>
         document.addEventListener('DOMContentLoaded', () => {
             const clickableImgs = document.querySelectorAll('.clickable-img');
             const lightbox = document.getElementById('qrLightbox');
             const lightboxImg = document.getElementById('qrLightboxImg');
             const closeBtn = document.getElementById('qrLightboxClose');

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

             // Click outside image closes
             lightbox.addEventListener('click', (e) => {
                 if (e.target === lightbox) {
                     lightbox.style.display = 'none';
                     lightboxImg.src = '';
                 }
             });
         });
     </script>
     <!-- End QR Picture clickable -->
     <td class="text-center">
         <div class="dropdown">
             <button class="btn p-0 border-0 bg-transparent text-gray" title="Actions" type="button" id="actionMenu{{ $inventory->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                 <i class="bi bi-three-dots-vertical"></i> <!-- 3-dot icon -->
             </button>

             <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenu{{ $inventory->id }}">

                 <!-- View Item -->
                 <li>
                     <button type="button" title="View Item" class="dropdown-item text-primary edit" data-url="{{ route('inventory.show', ['inventory' => $inventory->id]) }}">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye w-4 h-4">
                             <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                             <circle cx="12" cy="12" r="3"></circle>
                         </svg>
                         View
                     </button>
                 </li>

                 <!-- Edit Item -->
                 <li>
                     <button type="button" title="Edit Item" class="dropdown-item text-gray edit" data-url="{{ route('inventory.edit', ['inventory' => $inventory->id]) }}">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen w-4 h-4">
                             <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                             <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                         </svg>
                         Edit
                     </button>
                 </li>

                 <!-- Delete Item -->
                 <li>
                     <button type="button" title="Delete Item" class="dropdown-item text-danger delete" data-url="{{ route('inventory.destroy', $inventory->id) }}">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 lucide-trash-2 w-4 h-4">
                             <path d="M3 6h18"></path>
                             <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                             <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                             <line x1="10" x2="10" y1="11" y2="17"></line>
                             <line x1="14" x2="14" y1="11" y2="17"></line>
                         </svg>
                         Delete
                     </button>
                 </li>
             </ul>
         </div>
     </td>
 </tr>
 @endforeach
 @else
 <tr>
     <td colspan="12" class="text-center text-muted text-danger">{{ __('No Items found.') }}</td>
 </tr>
 @endif