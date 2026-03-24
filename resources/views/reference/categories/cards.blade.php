<div class="row g-3">
    @foreach($categories as $category)
    <div class="col-md-3" id="category-card-{{ $category->id }}">
        <div class="card shadow-sm rounded-3 pt-2 mb-1 h-100 border-0 card-styles">
            <div class="d-flex align-items-start">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1 px-2">
                        <!-- Category Type Badge -->
                        @if($category->type === 'consumable')
                        <span class="badge bg-success-subtle text-success">
                            <i class="bi bi-box-seam text-success me-2" style="font-size: 15px;"></i>Consumable
                        </span>
                        @elseif($category->type === 'non-consumable')
                        <span class="badge bg-primary-subtle text-primary">
                            <i class="bi bi-tag text-primary me-2" style="font-size: 15px;"></i>Non-Consumable
                        </span>
                        @endif

                        <!-- Edit/Delete Buttons -->
                        <div class="ms-auto text-center">
                            <!-- Edit Button -->
                            <button type="button"
                                class="btn p-0 border-0 bg-transparent text-gray edit"
                                data-url="{{ route('categories.edit', $category->id) }}"
                                title="Edit Category">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen me-2">
                                    <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                                </svg>
                            </button>

                            <!-- Delete Button -->
                            @if($category->inventories_count == 0)
                            <button type="button"
                                class="btn p-0 border-0 bg-transparent text-danger delete"
                                data-url="{{ route('categories.destroy', $category->id) }}"
                                title="Delete Category">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 me-2">
                                    <path d="M3 6h18"></path>
                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                    <line x1="10" x2="10" y1="11" y2="17"></line>
                                    <line x1="14" x2="14" y1="11" y2="17"></line>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="px-3 ms-2 mb-2">
                        <h6 class="fw-bold mb-0">{{ $category->name }}</h6>
                        <p class="mb-1 text-muted small">{{ $category->description ?? 'No description' }}</p>
                        <hr class="my-1" />
                        <small class="d-flex justify-content-between">
                            <span>Items in category</span>
                            <span class="fw-bold text-primary">{{ $category->inventories_count }}</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>