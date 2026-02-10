<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Units extends Model
{
    protected $table = 'units';

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'updated_by',
    ];
}