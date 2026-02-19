<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryNonConsumable extends Model
{
    protected $table = 'inventory_non_consumable';

    use SoftDeletes;

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

    public function qr_code()
    {
        return $this->belongsTo(QR_Code::class);
    }

    public function distribution()
    {
        return $this->hasOne(ItemDistribution::class, 'inventory_non_consumable_id');
    }
}
