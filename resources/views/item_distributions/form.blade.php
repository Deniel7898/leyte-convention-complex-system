<form method="POST" action="{{ route('item_distributions.store') }}">
    @csrf

    <div class="modal-header" style="background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">New Distribution</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">

        <div class="row">
            <!-- Select Item -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Select Item</label>
                <select name="item_id" id="itemSelect" class="form-select" required>
                    <option value="" selected>-- Select Item --</option>
                    
                    @foreach($items as $item)
                    <option value="{{ $item->id }}"
                        {{ isset($selectedItem) && $selectedItem->id == $item->id ? 'selected' : '' }}
                        data-type="{{ $item->type }}"
                        data-unit="{{ $item->unit->name ?? 'N/A' }}"
                        data-quantity="{{ $item->quantity }}"
                        data-consumables='@json($item->inventoryConsumables)'
                        data-nonconsumables='@json($item->inventoryNonConsumables)'>
                        {{ $item->name }}
                    </option>
                    @endforeach
                </select>

                <small class="text-muted">
                    Selecting an item will load its available units.
                </small>
            </div>

            <!-- Type -->
            <div class="col-md-6 mb-3">
                <label for="item-type" class="form-label">Type</label>
                <select class="form-select" id="itemDistribution-type" name="type" required>
                    <option value="" selected disabled>Select type</option>
                    <option value="0" {{ (isset($selectedItem) && $selectedItem->type == 0) ? 'selected' : '' }}>Distribution</option>
                    <option value="1" {{ (isset($selectedItem) && $selectedItem->type == 1) ? 'selected' : '' }}>Borrow</option>
                </select>
            </div>
        </div>

        <div class="row">
            <!-- Distribution Date -->
            <div class="col-md-6 mb-3">
                <label for="distribution-date" class="form-label">Distribution Date</label>
                <input type="date" class="form-control" id="distribution-date" name="distribution_date"
                    value="{{ isset($itemDistribution) ? $itemDistribution->distribution_date : date('Y-m-d') }}">
            </div>

            <!-- Distribution Status -->
            <div class="col-md-6 mb-3">
                <label for="distribution-status" class="form-label">Status</label>
                <select class="form-select" id="distribution-status" name="status" required>
                    <option value="" disabled selected>Select status</option>
                    <option value="pending" {{ (isset($itemDistribution) && $itemDistribution->status == 'pending') ? 'selected' : '' }}>Pending</option>
                    <option value="distributed" {{ (isset($itemDistribution) && $itemDistribution->status == 'distributed') ? 'selected' : '' }}>Distributed</option>
                    <option value="partial" {{ (isset($itemDistribution) && $itemDistribution->status == 'partial') ? 'selected' : '' }}>Partial</option>
                    <option value="borrowed" {{ (isset($itemDistribution) && $itemDistribution->status == 'borrowed') ? 'selected' : '' }}>Borrowed</option>
                    <option value="returned" {{ (isset($itemDistribution) && $itemDistribution->status == 'returned') ? 'selected' : '' }}>Returned</option>
                    <option value="received" {{ (isset($itemDistribution) && $itemDistribution->status == 'received') ? 'selected' : '' }}>Received</option>
                </select>
            </div>
        </div>

        <!-- Units Section (Hidden First) -->
        <div class="mb-3 d-none" id="unitsSection">

            <label class="form-label" id="unitsLabel"></label>

            <div class="mb-1">
                <small class="text-muted" id="itemInfo"></small>
            </div>

            <select name="inventory_ids[]"
                id="unitsSelect"
                class="form-select"
                multiple
                size="5">
            </select>

            <small class="text-muted">
                Hold Ctrl (Windows) or Cmd (Mac) to select multiple units.
            </small>
        </div>


        <!-- Description -->
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control"
                name="description"
                rows="2"
                placeholder="Optional description for this distribution"></textarea>
        </div>

        <!-- Remarks -->
        <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea class="form-control"
                name="remarks"
                rows="2"
                placeholder="Optional Remarks for this distribution"></textarea>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button"
            class="btn btn-secondary"
            data-bs-dismiss="modal">
            Close
        </button>

        <button type="submit"
            class="btn text-white"
            style="background-color: rgb(43, 45, 87);">
            Save Distribution
        </button>
    </div>
</form>

<script>
    document.getElementById('itemSelect').addEventListener('change', function() {

        let selected = this.options[this.selectedIndex];

        let type = selected.dataset.type;
        let quantity = selected.dataset.quantity;
        let unitName = selected.dataset.unit;
        let consumables = JSON.parse(selected.dataset.consumables || '[]');
        let nonConsumables = JSON.parse(selected.dataset.nonconsumables || '[]');

        let unitsSection = document.getElementById('unitsSection');
        let unitsSelect = document.getElementById('unitsSelect');
        let unitsLabel = document.getElementById('unitsLabel');
        let itemInfo = document.getElementById('itemInfo');

        unitsSelect.innerHTML = '';

        let allUnits = [...consumables, ...nonConsumables];

        if (allUnits.length === 0) {
            unitsSection.classList.add('d-none');
            return;
        }

        unitsSection.classList.remove('d-none');

        unitsLabel.innerText = 'Units for "' + selected.text + '"';

        itemInfo.innerText =
            'Type: ' + (type == 0 ? 'Consumable' : 'Non-Consumable') +
            ' | Unit: ' + unitName +
            ' | Quantity: ' + quantity +
            ' | Remaining: ' + allUnits.length;

        allUnits.forEach(unit => {
            let option = document.createElement('option');
            option.value = unit.id;
            option.text = 'Unit: ' + unit.id + ' | QR: ' + (unit.qr_code ?? 'N/A');
            unitsSelect.appendChild(option);
        });
    });
</script>