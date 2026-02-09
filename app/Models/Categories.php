<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    use SoftDeletes;
    
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'updated_by',
    ];
}
