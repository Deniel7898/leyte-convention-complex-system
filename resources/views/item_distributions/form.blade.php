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
            <!-- Select Category -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Select Category</label>
                <select id="categorySelect" class="form-select">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Selecting a category will filter available items.</small>
            </div>

            <!-- Select Item -->
            @if(!isset($itemDistribution))
            <div class="col-md-6 mb-3">
                <label class="form-label">Select Item</label>
                <select id="itemSelect" class="form-select" name="item_id" required>
                    <option value="">-- Select Item --</option>
                    @foreach($items as $item)
                    <option value="{{ $item->id }}"
                        data-unit="{{ $item->unit->name ?? 'N/A' }}"    
                        data-category="{{ $item->category_id }}"
                        data-inventories='@json($item->inventories->map(fn($inv) => ["id" => $inv->id, "qrCode" => optional($inv->qrCode)->code]))'>
                        {{ $item->name }}
                    </option>
                    @endforeach
                </select>
                <small class="text-muted">Selecting an item will load its available units.</small>
            </div>
            @else
            <div class="col-md-6 mb-3">
                <label class="form-label">Item Name</label>
                <input type="text" class="form-control" value="{{ $selectedItem->name ?? '' }}" readonly>
                <input type="hidden" name="item_id" value="{{ $selectedItem->id }}">
            </div>
            @endif
        </div>

        <div class="row">
            <!-- Quantity -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control" name="quantity" id="distributionQuantity"
                    value="{{ old('quantity', $itemDistribution->quantity ?? 1) }}" min="1" required>
            </div>

            <!-- Distribution Type -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Distribution Type</label>
                @if(!isset($itemDistribution))
                <select id="itemDistribution-type" class="form-select" name="type" required>
                    <option value="">Select type</option>
                    <option value="0">Distribution</option>
                    <option value="1">Borrow</option>
                </select>
                @else
                <input type="text" class="form-control" value="{{ $itemDistribution->type == 0 ? 'Distribution' : 'Borrow' }}" readonly>
                <input type="hidden" name="type" value="{{ $itemDistribution->type }}">
                @endif
            </div>
        </div>

        <!-- Hidden Status -->
        <input type="hidden" name="status" id="distributionStatus" value="{{ $itemDistribution->status ?? '' }}">

        <div class="row">
            <!-- Distribution Date -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Distribution Date</label>
                <input type="date" class="form-control" name="distribution_date"
                    value="{{ old('distribution_date', $itemDistribution->distribution_date ?? date('Y-m-d')) }}">
            </div>


        </div>

        <!-- Units Section -->
        <div class="mb-3 d-none" id="unitsSection">
            <label class="form-label fw-bold" id="unitsLabel"></label>
            <div class="mb-1"><small class="text-muted" id="itemInfo"></small></div>

            <div class="border rounded shadow-sm" style="max-height: 250px; overflow-y:auto; background-color:#f9f9f9;">
                <table class="table table-sm mb-0 align-middle text-center table-hover" id="unitsTable">
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
                        @if(isset($itemDistribution))
                        <tr>
                            <td>1</td>
                            <td>{{ $selectedItem->name ?? '' }}</td>
                            <td>{{ $itemDistribution->inventory->qrCode->code ?? 'N/A' }}</td>
                            <td>
                                <input type="hidden" name="inventory_ids[]" value="{{ $itemDistribution->inventory_id }}">
                                Selected
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <small class="text-muted d-block mt-1">Check the boxes to select multiple units.</small>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label class="form-label">Description (optional)</label>
            <textarea class="form-control" name="description" rows="2" placeholder="Optional description">{{ old('description', $itemDistribution->description ?? '') }}</textarea>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Save Distribution</button>
    </div>
</form>

<script>
    // ITEM SELECT CHANGE
    $(document).on('change', '#itemSelect', function() {
        const selected = this.options[this.selectedIndex];
        const unitName = selected.dataset.unit;
        const type = parseInt(selected.dataset.type || 0);
        const inventories = JSON.parse(selected.dataset.inventories || '[]');

        const unitsSection = $('#unitsSection');
        const unitsTableBody = $('#unitsTable tbody');
        const unitsLabel = $('#unitsLabel');
        const itemInfo = $('#itemInfo');
        const selectAllCheckbox = $('#selectAllUnits');

        unitsTableBody.html('');
        if (inventories.length === 0) {
            unitsSection.addClass('d-none');
            return;
        }

        unitsSection.removeClass('d-none');
        unitsLabel.text('Units for "' + selected.text + '"');
        itemInfo.text('Type: ' + (type === 0 ? 'Distribution' : 'Borrow') + ' | Unit: ' + unitName + ' | Remaining: ' + inventories.length);

        inventories.forEach((inv, idx) => {
            unitsTableBody.append(`
                <tr>
                    <td>${idx + 1}</td>
                    <td>${selected.text}</td>
                    <td>${inv.qrCode ?? 'N/A'}</td>
                    <td>
                        <input type="checkbox" class="unitCheckbox" name="inventory_ids[]" value="${inv.id}">
                    </td>
                </tr>
            `);
        });

        selectAllCheckbox.prop('checked', false);
    });

    // SELECT/DESELECT ALL
    $(document).on('change', '#selectAllUnits', function() {
        const checked = $(this).is(':checked');
        $('#unitsTable tbody .unitCheckbox').prop('checked', checked);
    });

    // STATUS FILTER ON TYPE CHANGE
    $(document).on('change', '#itemDistribution-type', function() {
        const type = $(this).val();
        const statusSelect = $('#distribution-status option');

        statusSelect.prop('disabled', false).show();

        if (type === '0') { // Distribution
            statusSelect.each(function() {
                if (['borrowed', 'returned'].includes($(this).val())) $(this).prop('disabled', true).hide();
            });
        } else if (type === '1') { // Borrow
            statusSelect.each(function() {
                if (['distributed', 'partial', 'pending'].includes($(this).val())) $(this).prop('disabled', true).hide();
            });
        }

        $('#distribution-status').val('');
    });
</script>


<script>
    $(document).on('change', '#categorySelect', function() {
        const selectedCategory = $(this).val();
        const itemSelect = $('#itemSelect');

        // Reset item select value
        itemSelect.val('');

        // Show all items if no category is selected
        if (!selectedCategory) {
            itemSelect.find('option').show();
            return;
        }

        // Loop through all item options
        itemSelect.find('option').each(function() {
            const optionCategory = $(this).data('category')?.toString();
            if (optionCategory === selectedCategory) {
                $(this).show();
            } else {
                // Hide options not in selected category
                $(this).hide();
            }
        });

        // Ensure the placeholder "-- Select Item --" remains visible
        itemSelect.find('option[value=""]').show();
    });
</script>



<script>
    // Update status and enforce quantity limit
    $(document).on('change', '#itemSelect, #distributionQuantity, #itemDistribution-type, .unitCheckbox, #selectAllUnits', function() {
        enforceQuantityLimit();
        updateStatus();
    });

    function enforceQuantityLimit() {
        const quantity = parseInt($('#distributionQuantity').val() || 0);
        const checkedBoxes = $('#unitsTable tbody .unitCheckbox:checked');
        const allBoxes = $('#unitsTable tbody .unitCheckbox');

        // If checked boxes exceed quantity, uncheck extras
        if (checkedBoxes.length > quantity) {
            // Uncheck the last selected ones
            checkedBoxes.slice(quantity).prop('checked', false);
        }

        // Disable remaining unchecked boxes if limit reached
        const checkedCount = $('#unitsTable tbody .unitCheckbox:checked').length;
        if (checkedCount >= quantity) {
            allBoxes.not(':checked').prop('disabled', true);
        } else {
            allBoxes.prop('disabled', false);
        }

        // If select all is checked, respect quantity
        const selectAll = $('#selectAllUnits');
        if (checkedCount === allBoxes.length || checkedCount === quantity) {
            selectAll.prop('checked', true);
        } else {
            selectAll.prop('checked', false);
        }
    }

    function updateStatus() {
        const type = parseInt($('#itemDistribution-type').val() || 0);
        const quantity = parseInt($('#distributionQuantity').val() || 0);
        const selectedCount = $('#unitsTable tbody .unitCheckbox:checked').length;

        let status = '';

        if (type === 1) { // Borrow
            status = 'borrowed';
        } else if (type === 0) { // Distribution
            if (selectedCount === 0) {
                status = 'pending';
            } else if (selectedCount < quantity) {
                status = 'partial';
            } else if (selectedCount >= quantity) {
                status = 'distributed';
            }
        }

        $('#distributionStatus').val(status);
    }
</script>