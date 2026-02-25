<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QR_Code extends Model
{
    use SoftDeletes;

    protected $table = 'qr_codes';

    protected $fillable = [
        'code',
        'status',
        'created_by',
        'updated_by',
        'expired_at',
        'inventory_consumable_id',
        'inventory_non_consumable_id',
    ];

    const STATUS_ACTIVE  = 'active';
    const STATUS_USED    = 'used';
    const STATUS_EXPIRED = 'expired';

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
