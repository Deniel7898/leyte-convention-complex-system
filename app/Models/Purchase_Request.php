<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase_Request extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_request';

    protected $fillable = [
        'request_date',
        'status',
        'items',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'request_date' => 'date',
        'items' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
