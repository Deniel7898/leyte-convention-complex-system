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
                    <label for="itemDistribution-type" class="form-label">Type</label>
                    <select class="form-select" id="itemDistribution-type" name="type" required>
                        <option value="">Select type</option>
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

                <label class="form-label fw-bold" id="unitsLabel"></label>

                <div class="mb-1">
                    <small class="text-muted" id="itemInfo"></small>
                </div>

                <div class="border rounded shadow-sm" style="max-height: 250px; overflow-y: auto; background-color: #f9f9f9;">
                    <table class="table table-sm mb-0 align-middle text-center" id="unitsTable">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Item Name</th>
                                <th>QR Code</th>
                                <th>
                                    <input type="checkbox" id="selectAllUnits" title="Select/Deselect All">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Rows injected by JS -->
                        </tbody>
                    </table>
                </div>

                <small class="text-muted d-block mt-1">
                    Check the boxes to select multiple units.
                </small>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label class="form-label">Description: (optional)</label>
                <textarea class="form-control"
                    name="description"
                    rows="2"
                    placeholder="Optional description for this distribution"></textarea>
            </div>

            <!-- Remarks -->
            <div class="mb-3">
                <label class="form-label">Remarks: (optional)</label>
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
        const itemSelect = document.getElementById('itemSelect');
        const unitsSection = document.getElementById('unitsSection');
        const unitsTableBody = document.querySelector('#unitsTable tbody');
        const unitsLabel = document.getElementById('unitsLabel');
        const itemInfo = document.getElementById('itemInfo');
        const selectAllCheckbox = document.getElementById('selectAllUnits');

        itemSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const type = selected.dataset.type;
            const quantity = selected.dataset.quantity;
            const unitName = selected.dataset.unit;
            const consumables = JSON.parse(selected.dataset.consumables || '[]');
            const nonConsumables = JSON.parse(selected.dataset.nonconsumables || '[]');

            unitsTableBody.innerHTML = '';

            const allUnits = [...consumables, ...nonConsumables];

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

            allUnits.forEach((unit, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
            <td>${index + 1}</td>
            <td>${selected.text}</td>
            <td>${unit.qr_code ?? 'N/A'}</td>
            <td>
                <input type="checkbox" class="unitCheckbox" name="inventory_ids[]" value="${unit.id}">
            </td>
        `;
                unitsTableBody.appendChild(tr);
            });

            // Reset "select all" checkbox
            selectAllCheckbox.checked = false;
        });

        // Select/Deselect All functionality
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.unitCheckbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>