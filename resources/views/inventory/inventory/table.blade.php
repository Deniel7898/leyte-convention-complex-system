 @if($inventories->count() > 0)
 @foreach($inventories as $inventory)
 <tr class="text-start">
     <td>{{ $inventories->firstItem() + $loop->index }}</td>
     <td>
         {{ $inventory->name ?? '--' }}
         @if(!empty($inventory->description))
         <br>
         <small class="text-muted"
             style="cursor: pointer;"
             data-bs-toggle="popover"
             data-bs-placement="top"
             data-bs-content="{{ $inventory->description }}">
             {{ Str::limit($inventory->description, 15, '...') }}
         </small>
         @endif
     </td>
     <td>
         <i class="bi bi-tag me-1"></i>
         {{ $inventory->category->name ?? '--' }}
     </td>
     <td>{{ $inventory->type ?? '--' }}</td>
     <td>{{ $inventory->unit->name ?? '--' }}</td>
     <td>{{ $inventory->total_stock ?? '--' }}</td>
     <td>{{ $inventory->remaining ?? '--' }}</td>
     @php
     $qrCodes = $inventory->qrCodes ?? collect();
     $qrCount = $qrCodes->count();
     @endphp

     <!-- Lightbox Overlay (shared for QR & Item Images) -->
     <div id="universalLightbox" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background: rgba(0,0,0,0.8); justify-content:center; align-items:center; z-index:1050;">
         <button id="universalLightboxClose" style="position:absolute; top:20px; right:20px; background:none;
    border:none; color:white; font-size:1.5rem; cursor:pointer;">&times;</button>
         <img id="universalLightboxImg" src="" style="max-width:90%; max-height:90%; border-radius:8px;">
     </div>

     <!-- Example QR Code Display -->
     <td @if($qrCount===1) style="padding:0; margin:0; vertical-align:top; text-align:center"
         @else style="text-align: center; vertical-align: middle;" @endif>
         @if($qrCount === 0)
         --
         @elseif($qrCount === 1)
         <img src="{{ asset('storage/' . $qrCodes->first()->qr_picture) }}"
             alt="QR Code"
             width="40"
             class="clickable-image"
             style="cursor:pointer;"
             data-full="{{ asset('storage/' . $qrCodes->first()->qr_picture) }}">
         <br>
         <small>{{ $qrCodes->first()->code }}</small>
         @else
         <i class="bi bi-qr-code text-muted"></i>
         <span class="text-muted">{{ $qrCount }} QR Codes</span>
         @endif
     </td>

     <!-- Example Item Picture Display -->
    <td style="padding:0; margin:0; text-align:center">
         @if($inventory->picture)
         <img src="{{ asset('storage/' . $inventory->picture) }}"
             alt="{{ $inventory->name }}"
             width="50"
             height="50"
             class="clickable-image"
             style="cursor:pointer;"
             data-full="{{ asset('storage/' . $inventory->picture) }}">
         @else
         <span>No Image</span>
         @endif
     </td>

     <script>
         document.addEventListener('DOMContentLoaded', () => {

             const lightbox = document.getElementById('universalLightbox');
             const lightboxImg = document.getElementById('universalLightboxImg');
             const closeBtn = document.getElementById('universalLightboxClose');

             const closeLightbox = () => {
                 lightbox.style.display = 'none';
                 lightboxImg.src = '';
             };

             document.addEventListener('click', (e) => {

                 // Open lightbox
                 const img = e.target.closest('.clickable-image');
                 if (img) {
                     lightboxImg.src = img.dataset.full;
                     lightbox.style.display = 'flex';
                     return;
                 }

                 // Close button
                 if (e.target === closeBtn) {
                     closeLightbox();
                 }

                 // Click outside image
                 if (e.target === lightbox) {
                     closeLightbox();
                 }

             });

         });
     </script>
     <td class="text-center">

         @if($inventory && $inventory->type == 'non-consumable')
         <!-- Issue Item -->
         <button type="button" title="Issue Item" class="btn p-0 border-0 bg-transparent text-success me-2 add-itemDistribution"
             data-url="{{ route('item_distributions.create') }}" data-item-id="{{ $inventory->id }}" data-type="issued">
             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send-plus" viewBox="0 0 16 16">
                 <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855a.75.75 0 0 0-.124 1.329l4.995 3.178 1.531 2.406a.5.5 0 0 0 .844-.536L6.637 10.07l7.494-7.494-1.895 4.738a.5.5 0 1 0 .928.372zm-2.54 1.183L5.93 9.363 1.591 6.602z" />
                 <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-3.5-2a.5.5 0 0 0-.5.5v1h-1a.5.5 0 0 0 0 1h1v1a.5.5 0 0 0 1 0v-1h1a.5.5 0 0 0 0-1h-1v-1a.5.5 0 0 0-.5-.5" />
             </svg>
         </button>

         <!-- Service Item -->
         <button type="button" title="Service Item" class="btn p-0 border-0 bg-transparent text-orange me-2 add-service"
             data-url="{{ route('service_records.create') }}" data-item-id="{{ $inventory->id }}">
             <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-wrench" viewBox="0 0 16 16">
                 <path d="M.102 2.223A3.004 3.004 0 0 0 3.78 5.897l6.341 6.252A3.003 3.003 0 0 0 13 16a3 3 0 1 0-.851-5.878L5.897 3.781A3.004 3.004 0 0 0 2.223.1l2.141 2.142L4 4l-1.757.364zm13.37 9.019.528.026.287.445.445.287.026.529L15 13l-.242.471-.026.529-.445.287-.287.445-.529.026L13 15l-.471-.242-.529-.026-.287-.445-.445-.287-.026-.529L11 13l.242-.471.026-.529.445-.287.287-.445.529-.026L13 11z" />
             </svg>
         </button>
         @endif

         @if($inventory && $inventory->type == 'consumable')
         <!-- Add Stock -->
         <button type="button" title="Restock Item" class="btn p-0 border-0 bg-transparent text-success me-2 add-stock"
             data-url="{{ route('inventory.show_stock') }}" data-item-id="{{ $inventory->id }}">
             <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="lucide lucide-plus-circle w-4 h-4">
                 <circle cx="12" cy="12" r="10"></circle>
                 <line x1="12" y1="8" x2="12" y2="16"></line>
                 <line x1="8" y1="12" x2="16" y2="12"></line>
             </svg>
         </button>

         <!-- Distribute -->
         <button type="button" title="Distribute Item" class="btn p-0 border-0 bg-transparent text-success me-2 add-itemDistribution"
             data-url="{{ route('item_distributions.create') }}" data-item-id="{{ $inventory->id }}" data-type="distributed">
             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send-plus" viewBox="0 0 16 16">
                 <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855a.75.75 0 0 0-.124 1.329l4.995 3.178 1.531 2.406a.5.5 0 0 0 .844-.536L6.637 10.07l7.494-7.494-1.895 4.738a.5.5 0 1 0 .928.372zm-2.54 1.183L5.93 9.363 1.591 6.602z" />
                 <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-3.5-2a.5.5 0 0 0-.5.5v1h-1a.5.5 0 0 0 0 1h1v1a.5.5 0 0 0 1 0v-1h1a.5.5 0 0 0 0-1h-1v-1a.5.5 0 0 0-.5-.5" />
             </svg>
         </button>
         @endif

         <!-- 3-dot Dropdown for Edit & View -->
         <div class="dropdown d-inline">
             <button class="btn p-0 border-0 bg-transparent text-gray" title="Actions" type="button"
                 id="actionMenu{{ $inventory->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                 <i class="bi bi-three-dots-vertical"></i>
             </button>

             <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenu{{ $inventory->id }}">
                 <!-- View -->
                 <li>
                     <a href="{{ route('items.show', $inventory->id) }}" class="dropdown-item text-primary" title="View Item">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye w-4 h-4 me-1">
                             <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                             <circle cx="12" cy="12" r="3"></circle>
                         </svg>
                         View
                     </a>
                 </li>

                 <!-- Edit -->
                 <li>
                     <button type="button" class="dropdown-item text-gray edit" data-url="{{ route('inventory.edit', ['inventory' => $inventory->id]) }}" title="Edit Item">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen w-4 h-4 me-1">
                             <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                             <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                         </svg>
                         Edit
                     </button>
                 </li>
             </ul>
         </div>
     </td>
 </tr>
 @endforeach
 @else
 <tr>
     <td colspan="10" class="text-center py-3">{{ __('No Items found.') }}</td>
 </tr>
 @endif