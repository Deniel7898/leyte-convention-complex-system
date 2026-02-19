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
        'used_at',
        'expired_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    const STATUS_ACTIVE  = 'active';
    const STATUS_USED    = 'used';
    const STATUS_EXPIRED = 'expired';

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Inventory - Consumable
    /*
    public function inventoryConsumable()
    {
        return $this->hasOne(InventoryConsumable::class, 'qr_code_id');
    }

    // Inventory - Non Consumable
    public function inventoryNonConsumable()
    {
        return $this->hasOne(InventoryNonConsumable::class, 'qr_code_id');
    }

    */

    // Creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Updater
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    
    /*
    |--------------------------------------------------------------------------
    | Smart Accessor: Get Related Item
    |--------------------------------------------------------------------------
    */

    /*
    public function getItemAttribute()
    {
        if ($this->inventoryConsumable && $this->inventoryConsumable->item) {
            return $this->inventoryConsumable->item;
        }

        if ($this->inventoryNonConsumable && $this->inventoryNonConsumable->item) {
            return $this->inventoryNonConsumable->item;
        }

        return null;
    }
    */

    /*
    |--------------------------------------------------------------------------
    | Status Methods
    |--------------------------------------------------------------------------
    */

    public function markAsUsed()
    {
        $this->update([
            'status' => self::STATUS_USED,
            'used_at' => now(),
            'updated_by' => auth()->id(),
        ]);
    }

    public function markAsExpired()
    {
        $this->update([
            'status' => self::STATUS_EXPIRED,
            'expired_at' => now(),
            'updated_by' => auth()->id(),
        ]);
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isUsed()
    {
        return $this->status === self::STATUS_USED;
    }

    public function isExpired()
    {
        return $this->status === self::STATUS_EXPIRED;
    }
}