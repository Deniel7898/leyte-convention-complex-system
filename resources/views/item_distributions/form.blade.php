<form action="{{ isset($itemDistribution) ? route('item_distributions.update', $itemDistribution->id) : route('item_distributions.store') }}"
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

        <!-- Item Name & Available Stock -->
        <div class="mb-3">
            <label class="form-label fw-bold">Item</label>
            <input type="text" class="form-control text-muted"
                value="{{ old('item_name', $selectedItem->name ?? $inventory->item->name ?? '') }}"
                readonly>
            <small class="text-muted">
                Available: {{ $selectedItem->remaining ?? $inventory->item->remaining ?? 0 }}
            </small>
            <input type="hidden" name="item_id" value="{{ $selectedItem->id ?? $inventory->item->id ?? '' }}">
        </div>

        <!-- Quantity (for consumables) -->
        @if(($selectedItem->type ?? '') === 'consumable')
        <div class="mb-3" id="quantityWrapper">
            <label class="form-label">Enter quantity</label>
            <input type="number" class="form-control" name="quantity" id="distributionQuantity"
                value="1" min="1" placeholder="Enter quantity" required>
        </div>
        @endif

        <!-- Units Table (for non-consumables) -->
        @if(($selectedItem->type ?? '') !== 'consumable')
        <div class="mb-3" id="unitsSection">
            <label class="form-label fw-bold">Select Units</label>
            <div class="border rounded shadow-sm" style="max-height: 200px; overflow-y:auto; background-color:#f9f9f9;">
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
                        @php
                        $counter = 1;
                        $availableInventories = collect(); // default empty collection

                        if ($selectedItem && $selectedItem->inventories) {
                        $availableInventories = $selectedItem->inventories->filter(function($inv) {
                        $hasActiveService = $inv->serviceRecords
                        ->whereIn('status', ['scheduled','in progress'])
                        ->count() > 0;

                        $hasDistributedOrIssued = $inv->itemDistributions
                        ->whereIn('type', ['distributed','issued'])
                        ->count() > 0;

                        $isBorrowed = strtolower($inv->status ?? '') === 'borrowed';

                        return !$hasActiveService && !$hasDistributedOrIssued && !$isBorrowed;
                        });
                        }

                        $singleInventory = $availableInventories->count() === 1 ? $availableInventories->first() : null;
                        @endphp

                        @forelse($availableInventories as $inventory)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>{{ $selectedItem->name }}</td>
                            <td>{{ $inventory->qrCode->code ?? 'N/A' }}</td>
                            <td>
                                @if($singleInventory)
                                <input type="hidden" name="inventory_ids[]" value="{{ $inventory->id }}">
                                <span class="text-success">Auto-selected</span>
                                @else
                                <input type="checkbox" class="unitCheckbox" name="inventory_ids[]" value="{{ $inventory->id }}">
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
        @endif

        <!-- Type -->
        @if(($selectedItem->type ?? '') !== 'consumable')
        <div class="mb-3" id="typeWrapper">
            <label class="form-label">Select type</label>
            <select class="form-select" name="type" id="itemDistribution-type" required>
                <option value="">-- Select type --</option>
                {{-- Hide Distributed for non-consumables --}}
                <option value="distributed" style="display:none;"
                    @if(isset($itemDistribution) && $itemDistribution->type == 'distributed') selected @endif>
                    Distributed
                </option>
                <option value="borrowed" @if(isset($itemDistribution) && $itemDistribution->type == 'borrowed') selected @endif>
                    Borrowed
                </option>
                <option value="issued" @if(isset($itemDistribution) && $itemDistribution->type == 'issued') selected @endif>
                    Issued
                </option>
            </select>
        </div>
        @else
        <input type="hidden" name="type" value="distributed">
        @endif

        <!-- Department / Borrower -->
        <div class="mb-3">
            <label class="form-label">Department or Borrower</label>
            <input type="text" class="form-control" name="department_or_borrower"
                value="{{ old('department_or_borrower', $itemDistribution->department_or_borrower ?? '') }}"
                placeholder="Enter department or borrower name" required>
        </div>

        <!-- Distribution Date -->
        <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" class="form-control" name="distribution_date"
                value="{{ old('distribution_date', $itemDistribution->distribution_date ?? date('Y-m-d')) }}">
        </div>

        <!-- Notes -->
        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea class="form-control" name="notes" rows="1"
                placeholder="Optional notes">{{ old('notes', $itemDistribution->notes ?? '') }}</textarea>
        </div>

        <!-- Status -->
        @if(($selectedItem->type ?? '') === 'consumable')
        <input type="hidden" name="status" value="completed">
        @else
        <input type="hidden" name="status" id="distributionStatus" value="">
        @endif

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Save Distribution</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {

        // Only run JS if type dropdown exists (non-consumables)
        if ($('#itemDistribution-type').length) {

            function toggleFields(type) {
                if ($('#quantityWrapper').length) $('#quantityWrapper').toggle(type === 'distributed');
                if ($('#unitsSection').length) $('#unitsSection').toggle(type !== 'distributed');

                let status = '';
                switch (type) {
                    case 'distributed':
                    case 'issued':
                        status = 'completed';
                        break;
                    case 'borrowed':
                        status = 'borrowed';
                        break;
                }
                $('#distributionStatus').val(status);
            }

            // Initial visibility & status
            let currentType = $('#itemDistribution-type').val();
            toggleFields(currentType);

            // On type change
            $(document).on('change', '#itemDistribution-type', function() {
                toggleFields($(this).val());
            });
        }

        // Select/Deselect All Units
        $('#selectAllUnits').change(function() {
            $('.unitCheckbox').prop('checked', $(this).is(':checked'));
        });
    });
</script>