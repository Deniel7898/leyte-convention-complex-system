<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service_Record extends Model
{
    use SoftDeletes;

    protected $table = 'service_records';

    protected $fillable = [
        'description',
        'quantity',
        'schedule_date',
        'completed_date',
        'encharge_person',
        'picture',
        'inventory_id', // single inventory reference
        'created_by',
        'updated_by',
    ];

    /**
     * Relationship to Inventory
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id')->with('item');
    }

    /**
     * Access the related item directly
     */
    public function item()
    {
        // Optional shortcut to get the item directly from inventory
        return $this->inventory ? $this->inventory->item : null;
    }

    /**
     * Created by user
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Optional: Get unit via inventory->item->unit
     */
    public function unit()
    {
        return $this->inventory && $this->inventory->item
            ? $this->inventory->item->unit
            : null;
    }
}
