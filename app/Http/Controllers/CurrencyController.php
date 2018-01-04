<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Swap\Laravel\Facades\Swap;

class CurrencyController extends Controller
{
    public function update($newCurrency)
    {
      if(sizeof(session('cart.item')) > 0) {
        $oldCurrency = session('currency');
        $rate = Swap::latest($oldCurrency.'/'.$newCurrency)->getValue();
        $total = 0;
        foreach(session('cart.item') as $index=>$item) {
          $newPrice = number_format((float)($item['price'] * $rate), 2, '.', '');
          session(["cart.item.$index.price" => $newPrice]);
          $total += $newPrice;
        }
        session(['cart.total' => $total]);
        //voucher code
        if(session('checkout.voucher.value')) {
          $voucherAmount = session('checkout.voucher.value');
          session(['checkout.voucher.value' => number_format((float)($voucherAmount * $rate), 2, '.', '')]);
        }
        //shipping cost
        if(session('cart.shipping.cost')) {
          switch(session('cart.shipping.location')) {
            case 'UK/US':
              if($newCurrency == 'USD') {
                session(['cart.shipping.cost' => 30]);
              }
              elseif($newCurrency == 'MYR') {
                session(['cart.shipping.cost' => 125]);
              }
              elseif($newCurrency == 'SGD') {
                session(['cart.shipping.cost' => 40.5]);
              }
              else {
                session(['cart.shipping.cost' => 25]);
              }
              break;

            case 'ASIAN':
              if($newCurrency == 'USD') {
                session(['cart.shipping.cost' => 20]);
              }
              elseif($newCurrency == 'MYR') {
                session(['cart.shipping.cost' => 82]);
              }
              elseif($newCurrency == 'SGD') {
                session(['cart.shipping.cost' => 27]);
              }
              else {
                session(['cart.shipping.cost' => 18]);
              }
              break;

            case 'EURO':
              if($newCurrency == 'USD') {
                session(['cart.shipping.cost' => 10]);
              }
              elseif($newCurrency == 'MYR') {
                session(['cart.shipping.cost' => 40.8]);
              }
              elseif($newCurrency == 'SGD') {
                session(['cart.shipping.cost' => 13.5]);
              }
              else {
                session(['cart.shipping.cost' => 8.50]);
              }
              break;
          }
        }
      }
      session(['currency' => $newCurrency]);
      session(['currencyRate' => $newCurrency == 'USD' ? 1 : Swap::latest($newCurrency.'/USD')->getValue()]);
    }
}
