<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LiveSale extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = [
        'is_running',
        'embed_code',
        'title',
        'slug',
        'product_id',
        'live_price',
        'payment_status'
    ];

    public function orders(){
        return $this->hasMany(LiveOrder::class);
    }
}
