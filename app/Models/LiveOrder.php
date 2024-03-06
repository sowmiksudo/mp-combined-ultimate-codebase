<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveOrder extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'phone',
        'accessor',
        'product_id',
        'user_id',
        'customer_info',
        'delivery_status',
        'live_sale_id'
    ];

    public function live_sale(){
        return $this->belongsTo(LiveSale::class)->withTrashed();
    }
}
