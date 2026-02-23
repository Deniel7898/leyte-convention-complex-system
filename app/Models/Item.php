<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'status',
        'quantity',
        'picture',
        'category_id',
        'unit_id',
        'created_by',
        'updated_by',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Units::class);
    }

     public function inventoryConsumables()
    {
        return $this->hasMany(InventoryConsumable::class, 'item_id');
    }

    public function inventoryNonConsumables()
    {
        return $this->hasMany(InventoryNonConsumable::class, 'item_id');
    }
}
