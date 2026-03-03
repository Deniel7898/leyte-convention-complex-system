<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service_Record extends Model
{
    protected $table = 'service_records';

    use SoftDeletes;

    protected $fillable = [
        'description',
        'quantity',
        'schedule_date',
        'completed_date',
        'encharge_person',
        'picture',
        'remarks',
        'inventory_non_consumable_id',
        'created_by',
        'updated_by',
    ];

    // Relationship to InventoryConsumable
    public function inventoryConsumable()
    {
        return $this->belongsTo(InventoryConsumable::class, 'inventory_consumable_id');
    }

    // Relationship to InventoryNonConsumable
    public function inventoryNonConsumable()
    {
        return $this->belongsTo(InventoryNonConsumable::class, 'inventory_non_consumable_id');
    }

    public function item()
    {
        if ($this->inventory_consumable_id) {
            return $this->belongsTo(InventoryConsumable::class, 'inventory_consumable_id')->with('item');
        }
        if ($this->inventory_non_consumable_id) {
            return $this->belongsTo(InventoryNonConsumable::class, 'inventory_non_consumable_id')->with('item');
        }

        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function unit()
    {
        return $this->belongsTo(Units::class);
    }
}
