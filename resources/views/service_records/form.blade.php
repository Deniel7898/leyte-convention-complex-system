<form action="{{ isset($service_record) ? route('service_records.update', $service_record->id) : route('service_records.store') }}" method="POST">
    @csrf
    @if(isset($service_record))
    @method('PUT')
    @endif

    <div class="modal-header" style="background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($service_record) ? 'Edit' : 'New' }} Item Service</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <!-- Item Selection (non-consumable) -->
        @if(!isset($service_record))
        <div class="mb-4">
            <label class="form-label fw-bold">Select Items for Service</label>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="border rounded shadow-sm" style="max-height: 250px; overflow-y: auto; background-color: #f9f9f9;">
                        <table class="table table-sm mb-0 align-middle text-center" id="unitsTable">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width:50px;">
                                        <input type="checkbox" id="select-all">
                                    </th>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>QR Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nonConsumables as $nonConsumable)
                                <tr>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="inventory_non_consumable_ids[]"
                                            value="{{ $nonConsumable->id }}"
                                            class="item-checkbox">
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $nonConsumable->item->name }}</td>
                                    <td>{{ $nonConsumable->qrCode->code ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="mb-4">
            <label class="form-label fw-bold">Selected Item</label>

            <input type="text" class="form-control"
                value="{{ $service_record->inventoryNonConsumable->item->name ?? 'N/A' }}"
                readonly>

            <!-- IMPORTANT: send the ID -->
            <input type="hidden"
                name="inventory_non_consumable_ids[]"
                value="{{ $service_record->inventory_non_consumable_id }}">
        </div>
        @endif

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $service_record->description ?? '') }}</textarea>
        </div>

        <!-- Schedule Date -->
        <div class="mb-3">
            <label for="schedule_date" class="form-label">Schedule Date</label>
            <input type="date" class="form-control" id="schedule_date" name="schedule_date"
                value="{{ old('schedule_date', isset($service_record) ? \Carbon\Carbon::parse($service_record->schedule_date)->format('Y-m-d') : '') }}" required>
        </div>

        <!-- Person in Charge -->
        <div class="mb-3">
            <label for="encharge_person" class="form-label">Person in Charge</label>
            <input type="text" class="form-control" id="encharge_person" name="encharge_person"
                value="{{ old('incharge_person', $service_record->encharge_person ?? '') }}" required>
        </div>

        <!-- Picture Upload -->
        <div class="mb-3">
            <label for="picture" class="form-label">Picture</label>
            <input type="file" class="form-control" id="picture" name="picture" accept="image/*">
            @if(isset($service_record) && $service_record->picture)
            <img src="{{ asset('storage/' . $service_record->picture) }}" alt="Current Picture" class="img-thumbnail mt-2" width="150">
            @endif
        </div>

        <!-- Hidden input for item ID -->
        <input type="hidden" name="item_id" value="{{ $selectedItem->id ?? ($service_record->id ?? '') }}">
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Save Item Service</button>
    </div>
</form>


<script>
    document.addEventListener("DOMContentLoaded", function() {

        const selectAll = document.getElementById('select-all');

        if (selectAll) {

            const checkboxes = document.querySelectorAll('.item-checkbox');

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                    toggleRowHighlight(checkbox);
                });
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    toggleRowHighlight(this);
                });
            });

            function toggleRowHighlight(checkbox) {
                if (checkbox.checked) {
                    checkbox.closest('tr').classList.add('table-primary');
                } else {
                    checkbox.closest('tr').classList.remove('table-primary');
                }
            }
        }
    });
</script>