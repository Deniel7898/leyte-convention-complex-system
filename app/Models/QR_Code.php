<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QR_Code extends Model
{
    use SoftDeletes;

    protected $table = 'qr_codes'; // 👈 ADD THIS

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

    public function inventoryConsumable()
    {
        return $this->hasOne(InventoryConsumable::class);
    }

    public function inventoryNonConsumable()
    {
        return $this->hasOne(InventoryNonConsumable::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

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
        ]);
    }

    public function markAsExpired()
    {
        $this->update([
            'status' => self::STATUS_EXPIRED,
            'expired_at' => now(),
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
