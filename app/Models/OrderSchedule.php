<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSchedule extends Model
{
    protected $fillable = ['order_id', 'date_order', 'date_delivery'];
}
