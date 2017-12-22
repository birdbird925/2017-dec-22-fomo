<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderItem extends Model
{
    protected $guarded = ['order_id'];
    protected $table = 'order_items';
    public $timestamps  = false;

    public function order()
    {
        return $this->belongsTo(order::class);
    }

    public function product()
    {
        return $this->belongsTo(CustomizeProduct::class);
    }
}
