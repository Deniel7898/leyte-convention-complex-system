<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Units extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'abbreviation',
        'description',
        'created_by',
        'updated_by',
    ];

    public function item()
    {
        return $this->hasMany(Item::class);
    }

    public function inventories()
    {
        return $this->hasManyThrough(
            Inventory::class, // Final model
            Item::class,      // Intermediate model
            'unit_id',    // Foreign key on items table
            'item_id',        // Foreign key on inventories table
            'id',             // Local key on categories
            'id'              // Local key on items
        );
    }
}
