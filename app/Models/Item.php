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
        return $this->hasMany(InventoryConsumable::class, 'item_id')
            ->whereDoesntHave('itemDistributions', function ($query) {
                $query->whereIn('status', ['distributed', 'borrowed', 'partial', 'pending']);
            });
    }

    public function inventoryNonConsumables()
    {
        return $this->hasMany(InventoryNonConsumable::class, 'item_id')
            ->whereDoesntHave('itemDistributions', function ($query) {
                $query->whereIn('status', ['distributed', 'borrowed', 'partial', 'pending']);
            });
    }

    protected static function booted()
    {
        // Soft-delete cascading
        static::deleting(function ($item) {
            // Delete consumables
            $item->inventoryConsumables->each->delete();

            // Delete non-consumables
            $item->inventoryNonConsumables->each->delete();
        });

        // Optional: restore inventories when item is restored
        static::restoring(function ($item) {
            $item->inventoryConsumables()->withTrashed()->get()->each->restore();
            $item->inventoryNonConsumables()->withTrashed()->get()->each->restore();
        });
    }
}