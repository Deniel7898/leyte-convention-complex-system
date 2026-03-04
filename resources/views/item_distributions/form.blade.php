<form action="{{ isset($itemDistribution) ? route('item_distributions.update', $itemDistribution->id) : route('item_distributions.store') }}"
    method="POST"
    enctype="multipart/form-data">

    @csrf
    @if(isset($itemDistribution))
    @method('PUT')
    @endif

    <div class="modal-header" style="background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($itemDistribution) ? 'Edit' : 'New' }} Distribution</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">

        <div class="row">
            <!-- Select Item -->
            @if(!isset($itemDistribution))
            <div class="col-md-6 mb-3">
                <label class="form-label">Select Item</label>
                <select
                    id="itemSelect"
                    class="form-select"
                    {{ isset($itemDistribution) ? 'disabled' : 'required' }}
                    name="{{ isset($itemDistribution) ? '' : 'item_id' }}">

                    <option value="">-- Select Item --</option>

                    @foreach($items as $item)
                    <option value="{{ $item->id }}"
                        data-type="{{ $item->type }}"
                        data-unit="{{ $item->unit->name ?? 'N/A' }}"
                        data-quantity="{{ $item->quantity }}"
                        data-consumables='@json($item->inventoryConsumables->map(fn($c) => ["id" => $c->id, "qrCode" => optional($c->qrCode)->code]))'
                        data-nonconsumables='@json($item->inventoryNonConsumables->map(fn($nc) => ["id" => $nc->id, "qrCode" => optional($nc->qrCode)->code]))'>
                        {{ $item->name }}
                    </option>
                    @endforeach
                </select>

                <small class="text-muted">
                    Selecting an item will load its available units.
                </small>
            </div>
            @else
            <div class="col-md-6 mb-3">
                <label for="item-name" class="form-label">Item Name</label>
                <input type="text" class="form-control" id="item-name" name="name" value="{{ $selectedItem->name ?? '' }}" required readonly>
            </div>
            @endif

            <!-- Type -->
            @if(!isset($itemDistribution))
            <!-- Select Item -->
            <div class="col-md-6 mb-3">
                <div class="col-md-6 mb-3">
                    <label for="itemDistribution-type" class="form-label">Type</label>
                    <select class="form-select" id="itemDistribution-type" name="type" required>
                        <option value="">Select type</option>
                        <option value="0" {{ (isset($selectedItem) && $selectedItem->type == 0) ? 'selected' : '' }}>Distribution</option>
                        <option value="1" {{ (isset($selectedItem) && $selectedItem->type == 1) ? 'selected' : '' }}>Borrow</option>
                    </select>
                </div>
                @else
                <div class="col-md-6 mb-3">
                    <label for="itemDistribution-type" class="form-label">Type</label>
                    <input type="text" class="form-control" value="{{ $selectedItem->type == 0 ? 'Distribution' : 'Borrow' }}" readonly>
                    <input type="hidden" name="type" value="{{ $selectedItem->type }}">
                </div>
                @endif

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

                    @php
                    $type = isset($itemDistribution) ? $itemDistribution->type : (isset($selectedItem) ? $selectedItem->type : null);
                    $currentStatus = $itemDistribution->status ?? null;

                    $statusOptions = [
                    'distributed' => 'Distributed',
                    'borrowed' => 'Borrow',
                    'partial' => 'Partial',
                    'pending' => 'Pending',
                    'returned' => 'Returned',
                    'received' => 'Received',
                    ];

                    $filteredOptions = [];

                    if ($type === null) {
                    $filteredOptions = $statusOptions; // adding new
                    } elseif ($type == 0) { // Distribution
                    $filteredOptions = collect($statusOptions)
                    ->except(['borrowed','returned','received'])
                    ->toArray();
                    } elseif ($type == 1) { // Borrow
                    $filteredOptions = collect($statusOptions)
                    ->except(['distributed','partial','pending'])
                    ->toArray();
                    }

                    // Ensure current status is always included
                    if ($currentStatus && !isset($filteredOptions[$currentStatus])) {
                    $filteredOptions[$currentStatus] = $statusOptions[$currentStatus];
                    }
                    @endphp

                    <select class="form-select" id="distribution-status" name="status" required>
                        <option value="" disabled {{ !$currentStatus ? 'selected' : '' }}>Select status</option>
                        @foreach($filteredOptions as $value => $label)
                        <option value="{{ $value }}" {{ ($currentStatus === $value) ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
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
                @if(isset($itemDistribution))
                <input type="hidden" name="inventory_ids[]"
                    value="{{ $itemDistribution->inventory_consumable_id ?? $itemDistribution->inventory_non_consumable_id }}">
                @endif
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
                    placeholder="Optional description for this distribution">{{ old('description', $itemDistribution->description ?? '') }}</textarea>
            </div>

            <!-- Remarks -->
            <div class="mb-3">
                <label class="form-label">Remarks: (optional)</label>
                <textarea class="form-control"
                    name="remarks"
                    rows="2"
                    placeholder="Optional Remarks for this distribution">{{ old('remarks', $itemDistribution->remarks ?? '') }}</textarea>
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
    // HANDLE ITEM SELECT CHANGE (AJAX SAFE)
    $(document).on('change', '#itemSelect', function() {
        const selected = this.options[this.selectedIndex];
        const type = selected.dataset.type;
        const quantity = selected.dataset.quantity;
        const unitName = selected.dataset.unit;
        const consumables = JSON.parse(selected.dataset.consumables || '[]');
        const nonConsumables = JSON.parse(selected.dataset.nonconsumables || '[]');

        const unitsSection = $('#unitsSection');
        const unitsTableBody = $('#unitsTable tbody');
        const unitsLabel = $('#unitsLabel');
        const itemInfo = $('#itemInfo');
        const selectAllCheckbox = $('#selectAllUnits');

        unitsTableBody.html('');

        const allUnits = [...consumables, ...nonConsumables];

        if (allUnits.length === 0) {
            unitsSection.addClass('d-none');
            return;
        }

        unitsSection.removeClass('d-none');

        unitsLabel.text('Units for "' + selected.text + '"');

        itemInfo.text(
            'Type: ' + (type == 0 ? 'Consumable' : 'Non-Consumable') +
            ' | Unit: ' + unitName +
            ' | Quantity: ' + quantity +
            ' | Remaining: ' + allUnits.length
        );

        allUnits.forEach((unit, index) => {
            unitsTableBody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${selected.text}</td>
                    <td>${unit.qrCode ?? 'N/A'}</td>
                    <td>
                        <input type="checkbox" class="unitCheckbox" name="inventory_ids[]" value="${unit.id}">
                    </td>
                </tr>
            `);
        });

        selectAllCheckbox.prop('checked', false);
    });

    // SELECT/DESELECT ALL HANDLER (put here)
    $(document).on('change', '#selectAllUnits', function() {
        var checked = $(this).is(':checked');
        $('#unitsTable tbody .unitCheckbox').prop('checked', checked);
    });
</script>

<script>
    $(document).on('change', '#itemDistribution-type', function() {
        var type = $(this).val(); // 0 = Distribution, 1 = Borrow
        var statusSelect = $('#distribution-status option');

        statusSelect.show(); // reset all first

        if (type == '0') { // Distribution
            statusSelect.each(function() {
                if (['borrowed', 'returned', 'received'].includes($(this).val())) {
                    $(this).hide();
                }
            });
        } else if (type == '1') { // Borrow
            statusSelect.each(function() {
                if (['distributed', 'partial', 'pending'].includes($(this).val())) {
                    $(this).hide();
                }
            });
        }

        // Reset selected
        $('#distribution-status').val('');
    });
</script>