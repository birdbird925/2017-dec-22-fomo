<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function lastOrder()
    {
        return $this->order()->orderBy('created_at', 'DESC')->first();
    }

    public function savedProduct()
    {
        return $this->hasMany(SavedProduct::class);
    }

    public function checkSavedProduct($id)
    {
        return $this->savedProduct()->where('product_id', $id)->count();
    }

    public function socialAccount()
    {
        return $this->hasOne(SocialAccount::class);
    }

    public function totalSpent()
    {
        $total = 0;
        foreach($this->order as $order)
            $total += $order->subTotal() + $order->shipping_cost;

        return $total;
    }

    public function totalItem()
    {
        $total = 0;
        foreach($this->order as $order)
            $total += $order->items->count();

        return $total;
    }

    public function successOrder()
    {
        return $this->order()->where('order_status', '1')->get();
    }

    public function successTotalSpent()
    {
        $total = 0;
        foreach($this->successOrder() as $order)
            $total += $order->subTotal() + $order->shipping_cost;

        return $total;
    }

    public function successTotalItem()
    {
        $total = 0;
        foreach($this->successOrder() as $order)
            $total += $order->items->count();

        return $total;
    }

    public function checkRole($role)
    {
        // 1 = buyer, 2 = admin
        if($role == 'buyer')
            return $this->role == 1;

        if($role == 'admin')
            return $this->role == 2;
    }


}
