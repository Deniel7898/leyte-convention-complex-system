 <form action="{{ isset($inventory) ? route('items.update', $inventory->id) : route('items.store') }}"
     method="POST">

     @csrf
     @if(isset($inventory))
     @method('PUT')
     @endif

     <div class="modal-header" style="background-color: rgb(43, 45, 87);">
         <h5 class="modal-title text-white">{{ isset($inventory) ? 'Edit' : 'Add' }} Unit</h5>
         <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
     </div>

     <div class="modal-body">
         <!-- Hidden input to pass current page segment -->
         <input type="hidden" name="page" id="currentPageInput">

         <script>
             function setCurrentSegment() {
                 const pageInput = document.getElementById('currentPageInput');
                 if (pageInput) {
                     const segments = window.location.pathname.replace(/^\/|\/$/g, '').split('/');
                     const firstSegment = segments[0] || 'inventory'; // fallback if empty
                     pageInput.value = firstSegment;
                 }
             }

             // Run immediately on page load
             setCurrentSegment();

             // If form is inside a Bootstrap modal, update on modal open
             const modal = document.getElementById('myFormModal');
             if (modal) {
                 modal.addEventListener('show.bs.modal', setCurrentSegment);
             }
         </script>

         <!-- Received Date -->
         <div class="mb-3">
             <label class="form-label">Received Date</label>
             <input type="date" class="form-control" name="received_date" value="{{ date('Y-m-d') }}" required>
         </div>

         <!-- Item Name (readonly) -->
         <div class="mb-3">
             <label class="form-label">Item</label>
             <input type="text" class="form-control" value="{{ $selectedItem->name ?? 'No Item Selected' }}" readonly>
         </div>

         <!-- Quantity -->
         @if(!isset($inventory)) <!-- Only show if not editing -->
         <div class="mb-3">
             <label class="form-label">Quantity</label>
             <input type="number" class="form-control" name="quantity" min="1" required>
         </div>
         @endif

         <!-- Holder/Department -->
         @if(isset($inventory))
         <div class="mb-3">
             <label class="form-label">Holder/Department</label>
             <input type="text" class="form-control" name="holder"
                 value="{{ $inventory->holder ?? '' }}">
         </div>
         @endif

         <!-- Date Assigned -->
         <div class="mb-3">
             <label class="form-label">Date Assigned</label>
             <input type="date" class="form-control" name="date_assigned"
                 value="{{ old('date_assigned', $itemDistribution->date_assigned ?? date('Y-m-d')) }}" required>
         </div>

         <!-- Notes -->
         <div class="mb-3">
             <label class="form-label">Notes</label>
             <input type="text" class="form-control" name="notes"
                 value="{{ $inventory->notes ?? '' }}"
                 placeholder="{{ isset($inventory) ? '' : 'Optional notes' }}">
         </div>

         <!-- Hidden Input for item reference -->
         <input type="hidden" name="item_id" value="{{ $selectedItem->id ?? '' }}">
     </div>

     <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
         <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Save Unit</button>
     </div>
 </form>