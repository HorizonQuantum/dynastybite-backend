<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'menu_id', 'quantity', 'price', 'total', 'pcs'];

    public function Order()
    {
        return $this->belongsTo(Order::class);
    }
}