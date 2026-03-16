<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QR_Code extends Model
{

    protected $table = 'qr_codes';

    protected $fillable = [
        'code',
        'qr_picture',
        'status',
        'created_by',
        'updated_by',
        'expired_at',
        'inventory_id', // single inventory reference
    ];

    const STATUS_ACTIVE  = 'active';
    const STATUS_USED    = 'used';
    const STATUS_EXPIRED = 'expired';

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id')->with('item');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
