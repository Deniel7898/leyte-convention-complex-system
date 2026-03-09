<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemDistribution extends Model
{
    use SoftDeletes;

    protected $table = 'item_distributions';

    protected $fillable = [
        'transaction_id',
        'item_id',        // general item reference
        'inventory_id',   // now points to unified inventory
        'description',
        'quantity',
        'distribution_date',
        'due_date',
        'returned_date',
        'status',         // pending, distributed, partial, borrowed, returned
        'created_by',
        'updated_by',
    ];

    // Unified Inventory relationship
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    // Original Item relationship
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}