<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryNonConsumable extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'inventory_non_consumable';

    protected $fillable = [
        'received_date',
        'item_id',
        'warranty_expires',
        'created_by',
        'updated_by',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function qrCode()
    {
        return $this->hasOne(QR_Code::class, 'inventory_non_consumable_id');
    }

    public function itemDistributions()
    {
        return $this->hasMany(ItemDistribution::class, 'inventory_non_consumable_id');
    }

    protected static function booted()
    {
        static::deleting(function ($inventory) {
            $inventory->qrCode?->delete(); // soft-delete QR code
        });

        static::restoring(function ($inventory) {
            $inventory->qrCode()->withTrashed()->first()?->restore();
        });
    }
}
