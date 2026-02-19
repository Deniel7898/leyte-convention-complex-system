<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryConsumable extends Model
{
    protected $table = 'inventory_consumable';

    use SoftDeletes;

    protected $fillable = [
        'received_date',
        'item_id',
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
        return $this->hasOne(ItemDistribution::class, 'inventory_consumable_id');
    }
}
