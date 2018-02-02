<?php

namespace App;


use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use Notifiable;

    protected $guarded = [];
    protected $table = 'orders';

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shipments()
    {
        return $this->hasMany(OrderShipment::class);
    }

    public function discount()
    {
      return $this->hasOne(VoucherHistory::class);
    }

    public function subTotal()
    {
        $total = 0;
        foreach($this->items as $item)
            $total += ($item->price * $item->quantity);
        return $total;
    }

    public function amount()
    {
        // (order subtotal + shipping cost - discount) * rate
        return $this->shipping_cost + $this->subTotal() - ($this->discount ? $this->discount->amount : 0);
    }

    public function fulfillStatus()
    {
        $fulfil = true;
        foreach($this->items as $item)
            if($item->quantity != $item->fulfill)
                $fulfil = false;

        return $fulfil;
    }

    public function orderCode()
    {
        return '#'.str_pad($this->id, 7, '0', STR_PAD_LEFT);
    }

}
