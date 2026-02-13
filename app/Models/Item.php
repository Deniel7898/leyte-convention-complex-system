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
        'availability',
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

    public function inventory_consumable()
    {
        return $this->hasMany(InventoryConsumable::class);
    }

    public function inventory_non_consumable()
    {
        return $this->hasMany(InventoryNonConsumable::class);
    }
}