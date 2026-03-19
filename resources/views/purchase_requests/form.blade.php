<form action="@isset($purchaseRequest){{ route('purchase-requests.update', $purchaseRequest->id) }}@else{{ route('purchase-requests.store') }}@endisset" method="POST" id="purchaseRequestForm">
    @csrf
    @isset($purchaseRequest)
        @method('PUT')
    @endisset

    <div class="modal-header pr-header">
        <h5 class="modal-title">
            @isset($purchaseRequest)
                Edit Purchase Request
            @else
                Add Purchase Request
            @endisset
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body pr-body">

        {{-- Request Date --}}
        <div class="form-group">
            <label>Request Date *</label>
            <input type="date"
                   name="request_date"
                   class="form-control pr-input"
                   value="@isset($purchaseRequest){{ $purchaseRequest->request_date->format('Y-m-d') }}@else{{ date('Y-m-d') }}@endisset"
                   required>
        </div>

        {{-- Items --}}
        <div class="form-group mt-4">
            <label class="fw-semibold">Purchase Request Items *</label>

            <div class="table-responsive">
                <table class="pr-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th width="100">Qty</th>
                            <th width="120">Unit</th>
                            <th>Description</th>
                            <th width="80" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="pr-items-body"></tbody>
                </table>
            </div>

            <button type="button" class="btn pr-add-btn mt-3" id="add-item-btn">
                + Add Item
            </button>
        </div>

    </div>

    <div class="modal-footer pr-footer">
        <button type="button" class="btn pr-close-btn" data-bs-dismiss="modal">
            Close
        </button>
        <button type="submit" class="btn pr-save-btn">
            @isset($purchaseRequest)
                Update Purchase Request
            @else
                Save Purchase Request
            @endisset
        </button>
    </div>
</form>

{{-- ======================= --}}
{{-- CSS INSIDE FORM --}}
{{-- ======================= --}}
<style>
.pr-header { background:#2b2d57; color:white; padding:15px 20px; }
.pr-body { background:#f4f6f9; padding:25px; }
.pr-footer { background:#f4f6f9; border-top:1px solid #e5e7eb; }

.pr-body label {
    font-weight:600;
    margin-bottom:6px;
    display:block;
    color:#374151;
}

.pr-input,
.pr-table input {
    border-radius:8px;
    border:1px solid #d1d5db;
    padding:8px 12px;
    font-size:14px;
    background:#fff;
}

.pr-input:focus,
.pr-table input:focus {
    border-color:#2b2d57;
    box-shadow:0 0 0 2px rgba(43,45,87,0.1);
}

.pr-table {
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius:10px;
    overflow:hidden;
}

.pr-table thead { background:#e5e7eb; }

.pr-table th {
    padding:12px;
    font-weight:600;
    font-size:14px;
    color:#374151;
}

.pr-table td {
    padding:10px;
    border-bottom:1px solid #f1f1f1;
}

.pr-add-btn {
    background:#2b2d57;
    color:white;
    border-radius:8px;
    padding:8px 14px;
    cursor:pointer;
}

.pr-add-btn:hover { background:#1f2145; }

.pr-close-btn {
    background:#6b7280;
    color:white;
    border-radius:8px;
    padding:8px 18px;
}

.pr-save-btn {
    background:#2b2d57;
    color:white;
    border-radius:8px;
    padding:8px 18px;
}

.pr-save-btn:hover { background:#1f2145; }

.remove-item {
    background:#dc3545;
    color:white;
    border:none;
    border-radius:6px;
    padding:5px 10px;
    cursor:pointer;
}

.remove-item:hover {
    background:#c82333;
}
</style>

{{-- ======================= --}}
{{-- SCRIPT WITH SUGGESTION --}}
{{-- ======================= --}}
<script>
let itemIndex = 0;

function getStoredItems() {
    try {
        return JSON.parse(localStorage.getItem('pr_items') || '[]');
    } catch (e) {
        console.warn('Invalid JSON in localStorage for pr_items, resetting:', e);
        localStorage.removeItem('pr_items');
        return [];
    }
}

function saveItem(name) {
    let items = getStoredItems();
    if (!items.includes(name)) {
        items.push(name);
        localStorage.setItem('pr_items', JSON.stringify(items));
    }
}

function getSuggestions() {
    return getStoredItems()
        .map(item => {
            const option = document.createElement('option');
            option.value = item;
            return option.outerHTML;
        })
        .join('');
}

function addItemRow() {
    let row = `
        <tr>
            <td>
                <input type="text"
                    name="items[${itemIndex}][item_name]"
                    class="form-control item-input"
                    placeholder="Enter item name..."
                    list="item-suggestions-${itemIndex}"
                    required>

                <datalist id="item-suggestions-${itemIndex}">
                    ${getSuggestions()}
                </datalist>
            </td>
            <td>
                <input type="number"
                    name="items[${itemIndex}][quantity]"
                    class="form-control text-center"
                    placeholder="Qty"
                    min="1"
                    required>
            </td>
            <td>
                <input type="text"
                    name="items[${itemIndex}][unit]"
                    class="form-control text-center"
                    placeholder="pcs / box"
                    required>
            </td>
            <td>
                <input type="text"
                    name="items[${itemIndex}][description]"
                    class="form-control"
                    placeholder="Optional description...">
            </td>
            <td class="text-center">
                <button type="button" class="remove-item">×</button>
            </td>
        </tr>
    `;

    $('#pr-items-body').append(row);
    itemIndex++;
}

function addItemRowWithData(item, index) {
    let row = `
        <tr>
            <td>
                <input type="text"
                    name="items[${index}][item_name]"
                    class="form-control item-input"
                    placeholder="Enter item name..."
                    list="item-suggestions-${index}"
                    value="${item.item_name || ''}"
                    required>

                <datalist id="item-suggestions-${index}">
                    ${getSuggestions()}
                </datalist>
            </td>
            <td>
                <input type="number"
                    name="items[${index}][quantity]"
                    class="form-control text-center"
                    placeholder="Qty"
                    value="${item.quantity || ''}"
                    min="1"
                    required>
            </td>
            <td>
                <input type="text"
                    name="items[${index}][unit]"
                    class="form-control text-center"
                    placeholder="pcs / box"
                    value="${item.unit || ''}"
                    required>
            </td>
            <td>
                <input type="text"
                    name="items[${index}][description]"
                    class="form-control"
                    placeholder="Optional description..."
                    value="${item.description || ''}">
            </td>
            <td class="text-center">
                <button type="button" class="remove-item">×</button>
            </td>
        </tr>
    `;

    $('#pr-items-body').append(row);
    itemIndex = index + 1;
}

$(function () {
    // Populate form with existing data if editing or add empty row for create
    @isset($purchaseRequest)
        const existingItems = {!! json_encode($purchaseRequest->items ?? []) !!};
        if (Array.isArray(existingItems) && existingItems.length > 0) {
            existingItems.forEach((item, index) => {
                addItemRowWithData(item, index);
            });
        } else {
            addItemRow();
        }
    @else
        addItemRow();
    @endisset

    // Add new row
    $('#add-item-btn').click(function () {
        addItemRow();
    });

    // Remove row
    $(document).on('click', '.remove-item', function () {
        $(this).closest('tr').remove();
    });

    // Save item to suggestion memory
    $(document).on('blur', '.item-input', function () {
        let value = $(this).val().trim();
        if (value !== '') {
            saveItem(value);
        }
    });

    // Prevent form submission if no items
    $('#purchaseRequestForm').on('submit', function(e) {
        const itemRows = $('#pr-items-body tr').length;
        if (itemRows === 0) {
            e.preventDefault();
            alert('Please add at least one item to the purchase request.');
            $('#add-item-btn').focus();
            return false;
        }
    });
});
</script>