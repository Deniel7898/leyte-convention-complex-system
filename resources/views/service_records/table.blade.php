 @if($service_records->count() > 0)
 @foreach($service_records as $service_record)
 <tr class="text-start">
     <td>{{ $loop->iteration }}</td>
     <td>{{ $service_record->item->item->name ?? '--' }}</td>
     <td>
         <i class="bi bi-tag me-1"></i>
         {{ $service_record->item->item->category->name ?? '--' }}
     </td>
     <td>
         @if($service_record->type == 0)
         <span class="badge bg-warning-subtle text-orange">Maintenance</span>
         @else
         <span class="badge bg-primary-subtle text-primary">Installation</span>
         @endif
     </td>
     <td>{{ $service_record->quantity ?? '--' }}</td>
     <td>{{ $service_record->item->qrCode->code ?? '--' }}</td>
     <td>
         @if($service_record->completed_date)
         <span class="badge bg-success-subtle text-success">Completed!</span>
         @else
         <span class="badge bg-warning-subtle text-orange">Pending!</span>
         @endif
     </td>
     <td>
         {{ $service_record->schedule_date && $service_record->schedule_date != '--'
            ? \Carbon\Carbon::parse($service_record->schedule_date)->format('M d, Y')
                : '--' }}
     </td>
     <td>{{ $service_record->description ?? '--' }}</td>
     <td>{{ $service_record->encharge_person ?? '--' }}</td>

     <!-- Start Service Picture -->
     <td style="padding:0; margin:0; vertical-align:top; text-align:center">
         @if($service_record->picture)
         <img src="{{ asset('storage/' . $service_record->picture) }}"
             alt="{{ $service_record->name }}"
             width="50"
             class="clickable-img"
             style="cursor: pointer;"
             data-full="{{ asset('storage/' . $service_record->picture) }}">
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
             const lightbox = document.getElementById('imgLightbox');
             const lightboxImg = document.getElementById('imgLightboxImg');
             const closeBtn = document.getElementById('imgLightboxClose');

             // Delegate clicks to all .clickable-img even if added later
             document.body.addEventListener('click', (e) => {
                 if (e.target.classList.contains('clickable-img')) {
                     lightboxImg.src = e.target.dataset.full;
                     lightbox.style.display = 'flex';
                 }
             });

             // Close lightbox
             closeBtn.addEventListener('click', () => {
                 lightbox.style.display = 'none';
                 lightboxImg.src = '';
             });

             // Close when clicking outside the image
             lightbox.addEventListener('click', (e) => {
                 if (e.target === lightbox) {
                     lightbox.style.display = 'none';
                     lightboxImg.src = '';
                 }
             });
         });
     </script>
     <!-- End Service Picture -->

     <td>
         {{ $service_record->completed_date && $service_record->completed_date != '--'
            ? \Carbon\Carbon::parse($service_record->completed_date)->format('M d, Y')
                : '--' }}
     </td>
     <td class="text-center">
         <div class="d-flex justify-content-center align-items-center gap-2">

             <!-- Completed Button -->
             @if(!$service_record->completed_date)
             <button type="button"
                 title="Complete Service"
                 class="btn p-0 border-0 bg-transparent text-success complete-service"
                 data-url="{{ route('service_records.complete', $service_record->id) }}"
                 data-item="{{ $service_record->inventoryNonConsumable->item->name ?? 'N/A' }}"
                 data-type="{{ $service_record->type }}"
                 data-qr="{{ $service_record->item->qrCode->code }}"
                 data-schedule="{{ \Carbon\Carbon::parse($service_record->schedule_date)->format('F d, Y') }}"
                 data-person="{{ $service_record->encharge_person }}">

                 <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                     <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                     <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05" />
                 </svg>
             </button>
             @endif

             <!-- Dropdown -->
             <div class="dropdown">
                 <button class="btn p-0 border-0 bg-transparent text-gray"
                     type="button"
                     id="actionMenu{{ $service_record->id }}"
                     data-bs-toggle="dropdown"
                     aria-expanded="false"
                     title="Actions">
                     <i class="bi bi-three-dots-vertical"></i>
                 </button>

                 <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                     aria-labelledby="actionMenu{{ $service_record->id }}">

                     <!-- Show only if completed_date is not null -->
                     @if(isset($service_record) && !is_null($service_record->completed_date))
                     <li>
                         <button type="button"
                             title="Undo Completion"
                             class="dropdown-item d-flex align-items-center text-warning undo-completion"
                             data-url="{{ route('service_records.undo', $service_record->id) }}"
                             data-item="{{ $service_record->inventoryNonConsumable->item->name ?? 'N/A' }}"
                             data-qr="{{ $service_record->item->qrCode->code }}"
                             data-schedule="{{ \Carbon\Carbon::parse($service_record->schedule_date)->format('F d, Y') }}"
                             data-person="{{ $service_record->encharge_person }}"
                             data-type="{{ $service_record->service_type }}">

                             <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-arrow-counterclockwise me-2" viewBox="0 0 16 16">
                                 <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2z" />
                                 <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466" />
                             </svg>

                             Undo Complete
                         </button>
                     </li>
                     @endif

                     <!-- View Item -->
                     <li>
                         <button type="button"
                             title="View Item"
                             class="dropdown-item d-flex align-items-center text-primary edit"
                             data-url="{{ route('service_records.show', $service_record->id) }}">
                             <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye me-2">
                                 <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                                 <circle cx="12" cy="12" r="3"></circle>
                             </svg>
                             View
                         </button>
                     </li>

                     <!-- Edit Item -->
                     <li>
                         <button type="button"
                             title="Edit Item"
                             class="dropdown-item d-flex align-items-center text-gray edit"
                             data-url="{{ route('service_records.edit', $service_record->id) }}">
                             <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen me-2">
                                 <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                 <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                             </svg>
                             Edit
                         </button>
                     </li>

                     <!-- Delete Item -->
                     <li>
                         <button type="button"
                             title="Delete Item"
                             class="dropdown-item d-flex align-items-center text-danger delete"
                             data-url="{{ route('service_records.destroy', ['service_record' => $service_record->id]) }}">
                             <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 me-2">
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

         </div>
     </td>
 </tr>
 @endforeach
 @else
 <tr>
     <td colspan="12" class="text-center text-muted text-danger">{{ __('No Service Records found.') }}</td>
 </tr>
 @endif