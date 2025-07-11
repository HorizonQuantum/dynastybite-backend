<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'total_price',
        'note_order',
        'user_id',
        'type_order_id',
        'status_id',
        'payment_method_id',
        'product_type_id',
        'address',
        'delivery_date',
        'periode',
        'payment_code',
        'expired_at',
        'is_paid',
    ];

    protected $casts = [
        'expired_at' => 'datetime:Y-m-d\TH:i:sP',
    ];


    // Relasi ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke tabel type_orders
    public function typeOrder()
    {
        return $this->belongsTo(TypeOrder::class);
    }

    // Relasi ke tabel order_statuses
    public function status()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
