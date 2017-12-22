<?php

namespace App;


use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderShipment extends Model
{

    protected $guarded = [];
    protected $table = 'order_shipments';

    public function items()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
