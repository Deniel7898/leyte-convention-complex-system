@extends('layouts.app')

@section('content')

<style>

/* ===== CONTAINER ===== */
#tableContainer {
    background: #f8f9fc;
    padding: 20px;
    border-radius: 12px;
}

/* ===== HEADER ===== */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.page-title {
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
}

/* ===== ADD BUTTON ===== */
#btnAdd {
    background: #2d2f5b;
    color: #fff;
    border-radius: 8px;
    padding: 8px 16px;
    font-weight: 500;
    border: none;
}

#btnAdd:hover {
    background: #1f2145;
}

/* ===== TABLE ===== */
.table {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
}

.table thead {
    background: #f1f3f9;
}

.table th {
    font-size: 12px;
    text-transform: uppercase;
    color: #6c757d;
    padding: 14px;
}

.table td {
    padding: 14px;
    font-size: 14px;
}

.table tbody tr:hover {
    background: #f9fbff;
}

/* ===== ITEMS ===== */
.item-name {
    font-weight: 600;
    color: #2d2f5b;
}

.badge-qty {
    background: #eef2ff;
    color: #2d6cdf;
    padding: 2px 6px;
    border-radius: 6px;
    font-size: 11px;
}

/* ===== ACTION ICONS ===== */
.action-btns {
    display: flex;
    gap: 14px;
}

.action-btns i {
    cursor: pointer;
    font-size: 16px;
    transition: 0.2s;
}

.icon-edit { color: #2d6cdf; }
.icon-print { color: #6c757d; }
.icon-delete { color: #dc3545; }

.action-btns i:hover {
    transform: scale(1.2);
}

/* ===== MODAL HEADER (FIXED) ===== */
.modal-header {
    background: #ffffff;
    border-bottom: 1px solid #e5e7eb;
    padding: 16px 20px;
}

.modal-title {
    font-size: 18px;
    font-weight: 700;
    color: #111827;
}

/* ===== MODAL BODY ===== */
.modal-body label {
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
}

.modal-body input {
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    padding: 8px;
    font-size: 13px;
}

/* ITEMS TABLE */
#itemsTable thead {
    background: #eef1f6;
}

/* ===== BUTTONS ===== */
.btn-save {
    background: #2563eb;
    color: #fff;
    border-radius: 8px;
    padding: 8px 16px;
    border: none;
}

.btn-close-custom {
    background: #6b7280;
    color: #fff;
    border-radius: 8px;
    padding: 8px 16px;
    border: none;
}

#addItem {
    background: #6b7280;
    color: #fff;
    border-radius: 8px;
    padding: 6px 12px;
    border: none;
}

.remove {
    background: #ef4444;
    color: #fff;
    border: none;
    border-radius: 6px;
}

/* ===== SPINNER (UNIFORM) ===== */
#loading-spinner {
    position: fixed;
    inset: 0;
    background: rgba(255,255,255,0.7);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

#loading-spinner.active {
    display: flex;
}

.spinner {
    border: 6px solid #f3f3f3;
    border-top: 6px solid #2d2f5b;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

</style>

<!-- ===== SPINNER ===== -->
<div id="loading-spinner">
    <div class="spinner"></div>
</div>

<!-- ===== HEADER ===== -->
<div class="page-header">
    <div class="page-title">Purchase Requests</div>
    <button id="btnAdd">+ Add Request</button>
</div>

<!-- ===== TABLE ===== -->
<div id="tableContainer">
    @include('purchase_requests.table')
</div>

<!-- ===== MODAL ===== -->
<div class="modal fade" id="requestModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title">Purchase Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">

                <input type="hidden" id="request_id">

                <label>Date</label>
                <input type="date" id="request_date" class="form-control mb-3">

                <table class="table" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <button id="addItem">+ Add Item</button>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
                <button class="btn-close-custom" data-bs-dismiss="modal">Close</button>
                <button id="btnSave" class="btn-save">Save</button>
            </div>

        </div>
    </div>
</div>

@endsection