<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Item extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'picture',
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
        return $this->hasOne(QR_Code::class, 'inventory_id');
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
            // Delete related inventories
            $item->inventories->each->delete();
        });

        static::restoring(function ($item) {
            $item->inventories()->withTrashed()->get()->each->restore();
        });
    }
}
