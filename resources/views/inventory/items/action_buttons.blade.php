<!-- Action Buttons -->
<div class="d-flex flex-wrap gap-2 mb-4">
    @if($item->type === 'non-consumable')
    <div class="col-auto">
        <div class="add-unit" data-url="{{ route('items.create') }}" data-item-id="{{ $item->id }}">
            <button class="btn px-3 text-white" style="background-color: hsl(237, 34%, 30%)"
                onmouseover="this.style.backgroundColor='hsl(237, 34%, 40%)'"
                onmouseout="this.style.backgroundColor='hsl(237, 34%, 30%)'">
                <i class="bi bi-plus-lg"></i> Add Unit
            </button>
        </div>
    </div>
    <div class="col-auto">
        <div class="add-itemDistribution" data-url="{{ route('item_distributions.create') }}" data-item-id="{{ $item->id }}" data-type="issued">
            <button class="btn px-3 text-white custom-btn">
                <i class="bi bi-send"></i> Issue/Borrow
            </button>
        </div>
    </div>
    <div class="col-auto">
        <div class="add-service" data-url="{{ route('service_records.create') }}" data-item-id="{{ $item->id }}">
            <button class="btn px-3 text-white custom-btn">
                <i class="bi bi-wrench"></i> Service
            </button>
        </div>
    </div>
    <div class="col-auto">
        <div>
            <button id="toggle-history" class="btn px-3 text-white custom-btn">
                <i class="bi bi-clock-history"></i> History
            </button>
        </div>
    </div>
    @else
    <div class="col-auto">
        <div class="add-stock" data-url="{{ route('inventory.show_stock') }}" data-item-id="{{ $item->id }}">
            <button class="btn px-3 text-white" style="background-color: hsl(237, 34%, 30%)"
                onmouseover="this.style.backgroundColor='hsl(237, 34%, 40%)'"
                onmouseout="this.style.backgroundColor='hsl(237, 34%, 30%)'">
                <i class="bi bi-plus-lg"></i>
                Restock
            </button>
        </div>
    </div>
    <div class="col-auto">
        <div class="add-itemDistribution" data-url="{{ route('item_distributions.create') }}" data-item-id="{{ $item->id }}" data-type="distributed">
            <button class="btn px-3 text-white custom-btn">
                <i class="bi bi-send"></i>
                Distribute
            </button>
        </div>
    </div>
    @endif
</div>