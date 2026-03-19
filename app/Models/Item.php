<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Item extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'total_stock',
        'remaining',
        'picture',
        'supplier',
        'notes',
        'category_id',
        'unit_id',
        'created_by',
        'updated_by',
    ];

    // Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Units::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'item_id');
    }

    public function qrCode()
    {
        return $this->hasOne(QR_Code::class, 'item_id', 'id');
    }

    /**
     * Available inventories (not distributed or borrowed)
     */
    public function availableInventories()
    {
        return $this->hasMany(Inventory::class, 'item_id')
            ->whereDoesntHave('itemDistributions', function ($query) {
                $query->whereIn('status', ['distributed', 'borrowed', 'partial', 'pending']);
            });
    }

    // Soft-delete cascading
    protected static function booted()
    {
        static::deleting(function ($item) {

            foreach ($item->inventories as $inventory) {

                // Delete distributions related to this inventory
                $inventory->itemDistributions()->delete();

                // Delete the inventory
                $inventory->delete();
            }
        });

        static::restoring(function ($item) {

            foreach ($item->inventories()->withTrashed()->get() as $inventory) {

                // Restore inventory
                $inventory->restore();

                // Restore distributions
                $inventory->itemDistributions()->withTrashed()->restore();
            }
        });
    }

    public function inventoryHistories()
    {
        return $this->hasMany(InventoryHistory::class, 'item_id');
    }

    public function serviceRecords()
    {
        return $this->hasMany(Service_Record::class, 'inventory_id', 'id');
    }
}
