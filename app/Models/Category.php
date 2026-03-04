<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'updated_by',
    ];

    public function item()
    {
        return $this->hasMany(Item::class);
    }

    public function inventoryConsumables()
    {
        return $this->hasManyThrough(
            InventoryConsumable::class, // Final model
            Item::class,                // Intermediate model
            'category_id',              // Foreign key on Item (to Category)
            'item_id',                  // Foreign key on InventoryConsumable (to Item)
            'id',                      // Local key on Category
            'id'                       // Local key on Item
        );
    }

    public function inventoryNonConsumables()
    {
        return $this->hasManyThrough(
            InventoryNonConsumable::class,
            Item::class,
            'category_id',
            'item_id',
            'id',
            'id'
        );
    }
}
