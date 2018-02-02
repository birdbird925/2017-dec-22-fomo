<?php

namespace App\Http\Controllers;

use App\Repositories\Image\ImageRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
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

        $total = 0;
        $priceField = session('currency').'_price';
        foreach(session('cart.item') as $item)
            $total += ($item[$priceField] * $item['quantity']);
        session(['cart.total' => $total]);

        return redirect('/cart');
    }

    public function updateQuantity($id, $quantity, Request $request)
    {
        $request->session()->put('cart.item.'.$id.'.quantity', $quantity);

        $total = 0;
        $priceField = session('currency').'_price';
        foreach(session('cart.item') as $item)
            $total += ($item[$priceField] * $item['quantity']);
        session(['cart.total' => $total]);
    }
}
