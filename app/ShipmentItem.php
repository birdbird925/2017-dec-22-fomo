<?php

namespace App;


use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShipmentItem extends Model
{

    protected $guarded = [];
    protected $table = 'order_shipment_items';
    public $timestamps  = false;

    public function shipment()
    {
        return $this->belongsTo(OrderShipment::class);
    }
}
