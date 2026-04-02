<div class="modal fade" id="requestModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Purchase Request</h5>
            </div>

            <div class="modal-body" id="printArea">

                <input type="hidden" id="request_id">

                <label>Date</label>
                <input type="date" id="request_date" class="form-control mb-2">

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

                <button id="addItem" class="btn btn-secondary">+ Add Item</button>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal">
                    Close
                </button>
                <button id="btnSave" class="btn-save">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>