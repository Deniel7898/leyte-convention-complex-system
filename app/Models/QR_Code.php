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

    /**
     * The consumable inventory this QR code belongs to.
     */
    public function inventoryConsumable()
    {
        return $this->belongsTo(InventoryConsumable::class, 'inventory_consumable_id');
    }

    /**
     * The non-consumable inventory this QR code belongs to.
     */
    public function inventoryNonConsumable()
    {
        return $this->belongsTo(InventoryNonConsumable::class, 'inventory_non_consumable_id');
    }
}