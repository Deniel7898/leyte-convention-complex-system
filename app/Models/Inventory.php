<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasUuids, SoftDeletes;

    public $incrementing = false;   // Disable auto-increment
    protected $keyType = 'string';  // Primary key is string (UUID)

    protected $fillable = [
        'received_date',
        'date_assigned',
        'due_date',
        'status',
        'holder',
        'notes',
        'item_id',
        'created_by',
        'updated_by',
    ];

    // Use 'id' for route binding (the UUID)
    public function getRouteKeyName()
    {
        return 'id';
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function qrCode()
    {
        return $this->hasOne(QR_Code::class, 'inventory_id', 'id');
    }

    public function itemDistributions()
    {
        return $this->hasMany(ItemDistribution::class, 'inventory_id');
    }

    public function serviceRecords()
    {
        return $this->hasMany(Service_Record::class, 'inventory_id');
    }

    protected static function booted()
    {
        static::deleting(function ($inventory) {

            // Delete QR Code
            $inventory->qrCode?->delete();

            // Delete related service records
            $inventory->serviceRecords()->delete();

            // Delete related item distributions
            $inventory->itemDistributions()->delete();
        });

        static::restoring(function ($inventory) {

            // Restore QR Code
            $inventory->qrCode()->withTrashed()->first()?->restore();

            // Restore service records
            $inventory->serviceRecords()->withTrashed()->restore();

            // Restore item distributions
            $inventory->itemDistributions()->withTrashed()->restore();
        });
    }
}
