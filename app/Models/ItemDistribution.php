<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemDistribution extends Model
{
    protected $table = 'item_distributions';

    use SoftDeletes;

    protected $fillable = [
        'item_id',
        'type',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];
}
