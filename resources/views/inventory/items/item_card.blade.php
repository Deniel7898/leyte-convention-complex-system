<div class="card shadow-lg rounded-4 p-3 mb-3 d-flex flex-column flex-md-row align-items-center gap-4 modern-card"
    id="item-card-{{ $item->id }}">
    <!-- Image -->
    <img src="{{ $item->picture ? asset('storage/'.$item->picture) : 'https://via.placeholder.com/120' }}"
        alt="{{ $item->name }}"
        class="rounded-4 border object-fit-cover clickable-image"
        style="width:120px;height:120px;cursor:pointer;"
        data-full="{{ $item->picture ? asset('storage/'.$item->picture) : 'https://via.placeholder.com/120' }}">
    <div class="flex-grow-1">
        @if($item->type === 'consumable')
        <span class="badge bg-success-subtle text-success">
            <i class="bi bi-box-seam me-1"></i> Consumable
        </span>
        @elseif($item->type === 'non-consumable')
        <span class="badge bg-primary-subtle text-primary">
            <i class="bi bi-tag me-1"></i> Non-Consumable
        </span>
        @endif
        @php
        $isConsumable = $item->type === 'consumable';
        @endphp
        <h2 class="fs-3">{{ $item->name }}</h2>
        <p class="text-muted"
            style="cursor: pointer;"
            data-bs-toggle="popover"
            data-bs-placement="top"
            data-bs-content="{{ $item->description }}">
            {{ \Illuminate\Support\Str::limit($item->description, 50, '...') }}
        </p>

        <div class="row text-muted small g-3">
            <!-- Category -->
            <div class="col-6 col-md-3">
                <strong>Category</strong><br>
                {{ $item->category->name ?? '--' }}
            </div>
            <!-- Unit -->
            <div class="col-6 col-md-3">
                <strong>Unit</strong><br>
                {{ $item->unit->name ?? '--' }}
            </div>
            @if(!$isConsumable)
            <!-- Total Units (non-consumables only) -->
            <div class="col-6 col-md-3">
                <strong>Total Units</strong><br>
                {{ $item->total_stock ?? 0 }}
            </div>
            <!-- Available Units -->
            <div class="col-6 col-md-3">
                <strong>Available Units</strong><br>
                <span class="text-success">{{ $item->remaining ?? 0 }}</span>
            </div>
            @else
            <!-- Remaining Stock (consumables only) -->
            <div class="col-6 col-md-3">
                <strong>Remaining</strong><br>
                <span class="text-success">{{ $item->remaining ?? 0 }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="col-auto">
        <div class="edit"
            data-url="{{ route('inventory.edit',['inventory'=>$item->id]) }}">
            <button class="btn px-4 text-white"
                style="background-color:hsl(237,34%,30%)">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen w-4 h-4 me-1">
                    <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                </svg>
                Edit Item
            </button>
        </div>
    </div>
    <div class="col-auto">
        <div class="delete-item" data-url="{{ route('items.destroy', ['item' => $item->id]) }}">
            @if(isset($item->historyCount) && $item->historyCount < 2)
            <button class="btn px-4 text-white" style="background-color:hsl(0,70%,50%)">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2 me-2">
                    <path d="M3 6h18"></path>
                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                    <line x1="10" x2="10" y1="11" y2="17"></line>
                    <line x1="14" x2="14" y1="11" y2="17"></line>
                </svg>
                Delete Item
            </button>
            @endif
        </div>
    </div>
</div>

