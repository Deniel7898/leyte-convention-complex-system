<div class="reports-grid" style="margin-bottom: 18px;">
    <div class="summary-card">
        <div class="summary-label">Total Items</div>
        <div class="summary-value">{{ number_format($totalItems) }}</div>
        <div class="summary-note">Distinct items in the inventory system</div>
    </div>

    <div class="summary-card">
        <div class="summary-label">Total Stock</div>
        <div class="summary-value">{{ number_format($totalStock) }}</div>
        <div class="summary-note">Combined stock from all items</div>
    </div>

    <div class="summary-card">
        <div class="summary-label">Remaining Stock</div>
        <div class="summary-value">{{ number_format($remainingStock) }}</div>
        <div class="summary-note">Current stock on hand</div>
    </div>

    <div class="summary-card">
        <div class="summary-label">Purchase Requests</div>
        <div class="summary-value">{{ number_format($purchaseRequestCount) }}</div>
        <div class="summary-note">Filtered by request date</div>
    </div>

    <div class="summary-card">
        <div class="summary-label">Pending PRs</div>
        <div class="summary-value">{{ number_format($pendingPurchaseRequests) }}</div>
        <div class="summary-note">Requests awaiting action</div>
    </div>

    <div class="summary-card">
        <div class="summary-label">Requested Quantity</div>
        <div class="summary-value">{{ number_format($totalRequestedQuantity) }}</div>
        <div class="summary-note">Total quantity from PR item lines</div>
    </div>

    <div class="summary-card">
        <div class="summary-label">Inventory Records</div>
        <div class="summary-value">{{ number_format($inventoryRecordCount) }}</div>
        <div class="summary-note">Total inventory entry records</div>
    </div>

    <div class="summary-card">
        <div class="summary-label">Distribution Transactions</div>
        <div class="summary-value">{{ number_format($distributionTransactionCount) }}</div>
        <div class="summary-note">Borrowed, issued, and distributed</div>
    </div>

    <div class="summary-card">
        <div class="summary-label">Distributed Quantity</div>
        <div class="summary-value">{{ number_format($distributedQuantity) }}</div>
        <div class="summary-note">Total quantity released</div>
    </div>

    <div class="summary-card">
        <div class="summary-label">Service Records</div>
        <div class="summary-value">{{ number_format($serviceRecordCount) }}</div>
        <div class="summary-note">Maintenance, installation, inspection</div>
    </div>
</div>