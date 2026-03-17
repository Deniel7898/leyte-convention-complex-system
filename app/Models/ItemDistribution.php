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
        'type',
        'item_id',       
        'inventory_id',   
        'quantity',
        'department_or_borrower',
        'distribution_date',
        'due_date',
        'returned_date',
        'status',         
        'notes',         
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
