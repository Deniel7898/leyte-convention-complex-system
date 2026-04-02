<div class="row g-3 mt-3" id="home-stats-cards">
    <div class="col-lg-3 col-md-6">
        <div class="stats-card primary">
            <div>
                <div class="stat-title">Total Items</div>
                <div class="stat-number">{{ number_format($stats['total_stock'] ?? 0) }}</div>
            </div>
            <a href="{{ route('inventory.index') }}" class="stat-icon"><i class="bi bi-box-seam"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-card success">
            <div>
                <div class="stat-title">Available</div>
                <div class="stat-number">{{ number_format($stats['total_remaining'] ?? 0) }}</div>
            </div>
            <a href="{{ route('inventory.index') }}" class="stat-icon"><i class="bi bi-check-circle"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-card warning">
            <div>
                <div class="stat-title">Items Service Required</div>
                <div class="stat-number">{{ number_format($stats['item_service_required'] ?? 0) }}</div>
            </div>
            <a href="{{ route('service_records.index') }}" class="stat-icon"><i class="bi bi-tools"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-card danger">
            <div>
                <div class="stat-title">To Purchase</div>
                <div class="stat-number">{{ $stats['to_purchase'] ?? 0 }}</div>
            </div>
            <a href="{{ route('purchase_requests.index') }}" class="stat-icon"><i class="bi bi-cart-dash"></i></a>
        </div>
    </div>
</div>