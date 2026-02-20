<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemDistribution extends Model
{
    protected $table = 'item_distributions';

    use SoftDeletes;

    protected $fillable = [
        'item_id',
        'type',
        'description',
        'quantity',
        'destribution_date',
        'due_date',
        'returned_date',
        'status',
        'remarks',
        'inventory_consumable_id',
        'inventory_non_consumable',
        'created_by',
        'updated_by',
    ];

    /**
     * Link to InventoryConsumable
     */
    public function inventory_consumable()
    {
        return $this->belongsTo(InventoryConsumable::class, 'inventory_consumable_id');
    }

    /**
     * Link to InventoryNonConsumable
     */
    public function inventory_non_consumable()
    {
        return $this->belongsTo(InventoryNonConsumable::class, 'inventory_non_consumable_id');
    }

    /**
     * Access the related Item from either inventory type
     */
    public function item()
    {
        return $this->inventory_non_consumable
            ? $this->inventory_non_consumable->item()
            : ($this->inventory_consumable ? $this->inventory_consumable->item() : null);
    }
}
