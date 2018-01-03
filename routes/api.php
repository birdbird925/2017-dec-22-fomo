<?php

use Illuminate\Http\Request;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');


Route::post('/checkout/api/paypal/payment/create', 'CheckoutController@paypal');
