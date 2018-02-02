<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Swap\Laravel\Facades\Swap;

class CurrencyController extends Controller
{
    public function update($newCurrency)
    {
        session(['currency' => $newCurrency]);
        session(['currencyRate' => $newCurrency == 'USD' ? 1 : Swap::latest($newCurrency.'/USD')->getValue()]);

        if(sizeof(session('cart.item')) > 0) {
            $total = 0;
            $priceField = session('currency').'_price';
            foreach(session('cart.item') as $item)
                $total += ($item[$priceField] * $item['quantity']);
            session(['cart.total' => $total]);
        }
    }
}
