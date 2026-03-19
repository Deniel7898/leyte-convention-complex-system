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
         <!-- Hidden input for current page segment -->
         <input type="hidden" name="page" id="currentPageInput" value="{{ request()->segment(1) ?? 'inventory' }}">

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
         @if(isset($inventory))
         <div class="mb-3">
             <label class="form-label">Date Assigned</label>
             <input type="date" class="form-control" name="date_assigned"
                 value="{{ old('date_assigned', $itemDistribution->date_assigned ?? date('Y-m-d')) }}" required>
         </div>
         @endif

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