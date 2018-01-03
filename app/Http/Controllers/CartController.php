<?php

namespace App\Http\Controllers;

use App\Repositories\Image\ImageRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use App\CustomizeType;
use App\CustomizeStep;
use App\CustomizeComponent;
use App\CustomizeComponentOption;
use App\Image;

class CartController extends Controller
{
    public function index()
    {
        return view('cart');
    }

    public function removeItem($id)
    {
        session()->forget("cart.item.$id");
        $price = 0;
        $price = session('cart.shipping.cost') > 0 ? session('cart.shipping.cost') : 0;
        foreach(session('cart.item') as $item) {
          $price += ($item['price'] * $item['quantity']);
        }
        session(['cart.total'=>$price]);
        return redirect('/cart');
    }

    public function updateQuantity($id, $quantity, Request $request)
    {
        $request->session()->put('cart.item.'.$id.'.quantity', $quantity);
        // session(['cart.item.'.$id.'.quantity' => $quantity]);
        $price = 0;
        $price = session('cart.shipping.cost') > 0 ? session('cart.shipping.cost') : 0;
        foreach(session('cart.item') as $item) {
          $price += ($item['price'] * $item['quantity']);
        }
        session(['cart.total'=>$price]);
    }

    public function updateShipping()
    {
        $price = request('cost');
        // if(session('currency') != 'USD') {
        //   $rate = Swap::latest('USD/'.session('currency'))->getValue();
        //   $price = number_format((float)(request('cost') * $rate), 2, '.', '');
        // }

        foreach(session('cart.item') as $item)
            $price += ($item['price'] * $item['quantity']);
        session([
            'cart.shipping.location'=>request('location'),
            'cart.shipping.cost'=>request('cost'),
            'cart.total'=> $price - request('cost')
        ]);

        return session('cart.total') + request('cost');
    }
}
