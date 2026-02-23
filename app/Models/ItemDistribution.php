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
        'distribution_date',
        'due_date',
        'returned_date',
        'status',
        'remarks',
        'inventory_consumable_id',
        'inventory_non_consumable',
        'created_by',
        'updated_by',
    ];

    public function inventory_consumable()
    {
        return $this->belongsTo(InventoryConsumable::class, 'inventory_consumable_id');
    }

    public function inventory_non_consumable()
    {
        return $this->belongsTo(InventoryNonConsumable::class, 'inventory_non_consumable_id');
    }

    public function item()
    {
        if ($this->inventory_consumable_id) {
            return $this->belongsTo(InventoryConsumable::class, 'inventory_consumable_id')
                ->withDefault(function ($consumable) {
                    $consumable->item = null;
                });
        }

        if ($this->inventory_non_consumable_id) {
            return $this->belongsTo(InventoryNonConsumable::class, 'inventory_non_consumable_id')
                ->withDefault(function ($nonConsumable) {
                    $nonConsumable->item = null;
                });
        }

        return $this->belongsTo(Item::class, 'id')->withDefault();
    }
}
