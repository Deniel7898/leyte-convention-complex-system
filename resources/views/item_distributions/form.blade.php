<form
    action="{{ isset($itemDistribution) ? route('item_distributions.update', $itemDistribution->id) : route('item_distributions.store') }}"
    method="POST" enctype="multipart/form-data" id="itemDistributionForm">
    @csrf
    @if(isset($itemDistribution))
        @method('PUT')
    @endif

    <div class="modal-header" style="background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($itemDistribution) ? 'Edit' : 'New' }} Distribution</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="p-4 bg-white rounded shadow-sm">
        <!-- Hidden input for current page segment -->
        <input type="hidden" name="page" id="currentPageInput" value="{{ request()->segment(1) ?? 'inventory' }}">

        <!-- Hidden input for service ID -->
        <input type="hidden" name="distribution_id" value="{{ $itemDistribution->id ?? '' }}">

        <div class="row">
            <!-- Item Name & Available Stock -->
            <div class="col-md-6 mb-1">
                <label class="form-label bold-label">Item</label>

                @if(isset($selectedItem))
                    <input type="text" class="form-control text-muted"
                        value="{{ old('item_name', $selectedItem->name ?? $inventory->item->name ?? '') }}" readonly>
                    <small class="text-muted">
                        Available: {{ $selectedItem->remaining ?? $inventory->item->remaining ?? 0 }}
                    </small>
                    <input type="hidden" name="item_id" value="{{ $selectedItem->id ?? $inventory->item->id ?? '' }}">
                @else
                    <select class="form-select" name="item_id" id="itemSelect" required>
                        <option value="" disabled {{ !isset($itemDistribution) ? 'selected' : '' }} hidden>Select an Item
                        </option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-type="{{ $item->type }}"
                                data-remaining="{{ $item->remaining }}">
                                {{ $item->name }} (Available: {{ $item->remaining }})
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- Type -->
            @if(($selectedItem->type ?? '') !== 'consumable')
                <div class="col-md-6 mb-1" id="typeWrapper">
                    <label class="form-label required">Select type</label>
                    <select class="form-select" name="type" id="itemDistribution-type" required>
                        <option value="" disabled {{ !isset($itemDistribution) ? 'selected' : '' }} hidden>Select Type</option>
                        {{-- Hide Distributed for non-consumables --}}
                        <option value="distributed" style="display:none;" @if(isset($itemDistribution) && $itemDistribution->type == 'distributed') selected @endif>
                            Distributed
                        </option>
                        <option value="borrowed" @if(isset($itemDistribution) && $itemDistribution->type == 'borrowed')
                        selected @endif>
                            Borrowed
                        </option>
                        <option value="issued" @if(isset($itemDistribution) && $itemDistribution->type == 'issued') selected
                        @endif>
                            Issued
                        </option>
                    </select>
                </div>
            @else
                <input type="hidden" name="type" value="distributed">
            @endif


            <!-- Quantity (for consumables) -->
            @if(($selectedItem->type ?? '') === 'consumable')
                <div class="col-md-6 mb-3" id="quantityWrapper">
                    <label class="form-label required">Quantity</label>
                    <input type="number" class="form-control" name="quantity" id="distributionQuantity" value="" min="1"
                        max="{{ $selectedItem->remaining }}" placeholder="Enter quantity" required
                        @if($selectedItem->remaining <= 0) disabled @endif>
                </div>
            @endif

            <!-- Units Table (for non-consumables) -->
            @php
                $availableInventories = collect(); // default empty collection

                if ($selectedItem && $selectedItem->inventories) {
                    $availableInventories = $selectedItem->inventories->filter(function ($inv) {
                        $hasActiveService = $inv->serviceRecords
                            ->whereIn('status', ['scheduled', 'in progress'])
                            ->count() > 0;

                        $hasDistributedOrIssued = $inv->itemDistributions
                            ->whereIn('type', ['distributed'])
                            ->count() > 0;

                        // Check if status is borrowed or issued
                        $status = strtolower($inv->status ?? '');
                        $isBorrowedOrIssued = in_array($status, ['borrowed', 'issued']);

                        return !$hasActiveService && !$hasDistributedOrIssued && !$isBorrowedOrIssued;
                    });
                }

                $inventoriesCount = $availableInventories->count();
                $singleInventory = $inventoriesCount === 1 ? $availableInventories->first() : null;

                // Only show units table if more than one inventory is available
                $showUnitsTable = ($selectedItem->type ?? '') !== 'consumable' && $inventoriesCount > 0 && !empty($selectedItem) && optional($selectedItem->inventories)->count() > 0 && empty($quickAction);
            @endphp

            @if($showUnitsTable)
                <div class="mb-3" id="unitsSection">
                    <label class="form-label required">Select Units</label>
                    <div class="border rounded shadow-sm"
                        style="max-height: 200px; overflow-y:auto; background-color:#f9f9f9;">
                        <table class="table table-sm mb-0 text-center align-middle">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>QR Code</th>
                                    <th>
                                        <input type="checkbox" id="selectAllUnits" title="Select/Deselect All">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $counter = 1; @endphp

                                @forelse($availableInventories as $inventory)
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $selectedItem->name }}</td>
                                        <td>{{ $inventory->qrCode->code ?? 'N/A' }}</td>
                                        <td>
                                            @if(isset($selectedInventory) && $inventory->id == $selectedInventory)
                                                <input type="hidden" name="inventory_ids[]" value="{{ $inventory->id }}">
                                                <span class="text-success">Auto-selected</span>
                                            @else
                                                <input type="checkbox" class="unitCheckbox" name="inventory_ids[]"
                                                    value="{{ $inventory->id }}">
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">No available units</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif(isset($selectedInventory))
                <input type="hidden" name="inventory_ids[]" value="{{ $selectedInventory }}">
            @endif


            <!-- Department / Borrower -->
            <div class="col-md-6 mb-3">
                <label class="form-label required">Department or Borrower</label>
                <input type="text" class="form-control" name="department_or_borrower"
                    value="{{ old('department_or_borrower', $itemDistribution->department_or_borrower ?? '') }}"
                    placeholder="Enter department or borrower name" required>
            </div>

            <!-- Distribution Date -->
            <div class="col-md-6 mb-3">
                <label class="form-label required">Distribution Date</label>
                <input type="date" class="form-control" name="distribution_date"
                    value="{{ old('distribution_date', $itemDistribution->distribution_date ?? date('Y-m-d')) }}"
                    min="{{ date('Y-m-d') }}">
            </div>

            <!-- Due Date -->
            <div class="mb-3" id="dueDateWrapper" style="display:none;">
                <label class="form-label required">Due Date</label>
                <input type="date" class="form-control" name="due_date"
                    value="{{ old('due_date', $itemDistribution->due_date ?? '') }}" min="{{ date('Y-m-d') }}">
            </div>

            <!-- Notes -->
            <div class="mb-3">
                <label class="form-label bold-label">Notes</label>
                <textarea class="form-control" name="notes" rows="1"
                    placeholder="Optional notes">{{ old('notes', $itemDistribution->notes ?? '') }}</textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Save
                    Distribution</button>
            </div>
        </div>
    </div>
</form>
<script>
    function toggleFieldsByType(type) {
        const unitsSection = $('#unitsSection');
        const quantityWrapper = $('#quantityWrapper');
        const distributionQuantity = $('#distributionQuantity');

        // Hide everything by default
        if (unitsSection.length) unitsSection.hide();
        if (quantityWrapper.length) quantityWrapper.hide();
        if (distributionQuantity.length) distributionQuantity.val(1);
        $('.unitCheckbox').prop('checked', false);
        $('#selectAllUnits').prop('checked', false);

        // Show/hide based on type
        switch (type) {
            case 'issued':
                if (unitsSection.length) unitsSection.hide();
                if (quantityWrapper.length) quantityWrapper.hide();
                if (distributionQuantity.length) distributionQuantity.val(1);
                break;

            case 'distributed':
                if (unitsSection.length) unitsSection.hide();
                if (quantityWrapper.length) quantityWrapper.show();
                if (distributionQuantity.length) distributionQuantity.val(1);
                break;

            case 'borrowed':
                if (unitsSection.length) unitsSection.hide();
                if (quantityWrapper.length) quantityWrapper.hide();
                if (distributionQuantity.length) distributionQuantity.val(1);
                // Reset item select if general item
                if ($('#itemSelect').length) $('#itemSelect').prop('selectedIndex', 0);
                break;

            default:
                if (unitsSection.length) unitsSection.hide();
                if (quantityWrapper.length) quantityWrapper.hide();
                if (distributionQuantity.length) distributionQuantity.val(1);
                break;
        }
    }

    // --- SELECT/DESELECT ALL UNITS (global) ---
    $(document).ready(function () {
        // Initialize form on page load
        const currentType = $('#itemDistribution-type').val();
        if (currentType) {
            toggleFieldsByType(currentType); // sets the status based on type
        }

        // Select/Deselect all units
        $('#selectAllUnits').change(function () {
            $('.unitCheckbox').prop('checked', $(this).is(':checked'));
        });
    });
</script>
<script>
    (function () {
        const input = document.getElementById('distributionQuantity');
        if (!input) return;

        const max = Number(input.getAttribute('max')) || 1;

        input.addEventListener('input', function () {
            let value = Number(this.value);

            if (isNaN(value) || value < 1) {
                this.value = ''; // keep empty if less than min
            } else if (value > max) {
                this.value = max; // force it back to max
            }
        });
    })();
</script>
<script>
    // Show/hide Due Date based on type
    function toggleDueDate(type) {
        const dueDateWrapper = document.getElementById('dueDateWrapper');

        if (!dueDateWrapper) return;

        if (type === 'borrowed') {
            dueDateWrapper.style.display = 'block';
        } else {
            dueDateWrapper.style.display = 'none';
        }
    }

    // On change
    document.getElementById('itemDistribution-type')?.addEventListener('change', function () {
        toggleDueDate(this.value);
    });

    // On load (for edit mode)
    document.addEventListener('DOMContentLoaded', function () {
        const type = document.getElementById('itemDistribution-type')?.value;
        toggleDueDate(type);
    });
</script>