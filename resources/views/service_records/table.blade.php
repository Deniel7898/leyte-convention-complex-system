 @if($service_records->count() > 0)
 @foreach($service_records as $service_record)
 <tr class="text-start">
     <td>{{ $loop->iteration }}</td>
     <td>{{ $service_record->item->item->name ?? '--' }}</td>
     <td>{{ $service_record->item->item->unit->name ?? '--' }}</td>
     <td>
         <i class="bi bi-tag me-1"></i>
         {{ $service_record->item->item->category->name ?? '--' }}
     </td>
     <td>
         <span class="badge bg-success-subtle text-success">
             {{ $service_record->item->qrCode->code ?? '--' }}
         </span>
     </td>
     <td>{{ $service_record->quantity ?? '--' }}</td>
     <td>{{ $service_record->status ?? '--' }}</td>
     <td>{{ $service_record->description ?? '--' }}</td>
     <td>
         {{ $service_record->schedule_date && $service_record->schedule_date != '--'
            ? \Carbon\Carbon::parse($service_record->schedule_date)->format('M d, Y')
                : '--' }}
     </td>
     <td>
         {{ $service_record->completed_date && $service_record->completed_date != '--'
            ? \Carbon\Carbon::parse($service_record->completed_date)->format('M d, Y')
                : '--' }}
     </td>
     <td>{{ $service_record->encharge_person ?? '--' }}</td>
     <td>{{ $service_record->picture ?? '--' }}</td>
     <td>{{ $service_record->remarks ?? '--' }}</td>
     <td class="text-center">
         <a href=""
             title="View Item"
             class="btn p-0 border-0 bg-transparent text-primary me-2">
             <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye w-4 h-4">
                 <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                 <circle cx="12" cy="12" r="3"></circle>
             </svg>
         </a>
         <button type="button" title="Edit Item" class="btn p-0 border-0 bg-transparent text-gray me-2 edit" data-url="{{ route('service_records.edit', $service_record->id) }}">
             <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen w-4 h-4">
                 <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                 <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
             </svg>
         </button>
         <button type="button" title="Delete Item" class="btn p-0 border-0 bg-transparent text-danger delete" data-url="{{route('service_records.destroy', ['service_record' => $service_record->id])}}">
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
     <td colspan="12" class="text-center text-muted text-danger">{{ __('No Service Records found.') }}</td>
 </tr>
 @endif