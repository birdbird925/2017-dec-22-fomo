<?php

// home
Route::get('/', 'HomeController@index');
// switch currency
Route::get('/currency/{currency}/update', 'CurrencyController@update');
// login
Route::get('login', function(){return redirect('/');});
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout');
Route::get('register', function(){return redirect('/');});
Route::post('register', 'Auth\RegisterController@register');
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/reset', 'Auth\ResetPasswordController@customReset');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/email', 'Auth\ForgotPasswordController@customSendResetLinkEmail')->name('password.email');
// socialite route
Route::get('login/{socialProvider}', 'Auth\LoginController@redirectToProvider');
Route::get('login/{socialProvider}/callback', 'Auth\LoginController@handleProviderCallback');
// cart
Route::get('/cart', 'CartController@index');
Route::post('/cart/shipping/update', 'CartController@updateShipping');
Route::post('/cart/{id}/remove', 'CartController@removeItem');
Route::get('/cart/{id}/quantity/{quantity}/update', 'CartController@updateQuantity');
// checkout
Route::post('/checkout/validation', 'CheckoutController@validation');
Route::get('/checkout', 'CheckoutController@checkout');
Route::post('/discount-code/apply', 'CheckoutController@submitVoucher');
Route::post('/checkout/shipping/save', 'CheckoutController@saveShipping');
Route::post('/checkout/paypal/payment/create', 'CheckoutController@paypal');
Route::post('/checkout/done', 'CheckoutController@getDone');
// Route::get('/checkout/refund', 'CheckoutController@refund');
//customize
Route::get('/customize', 'CustomizeController@index');
Route::get('/customize/{id}', 'CustomizeController@edit');
Route::post('/cart/add', 'CustomizeController@addCart');
// Route::post('/cart/{id}/update', 'CustomizeController@updateCart');
Route::post('/product/save', 'CustomizeController@saveProduct');
// Route::post('/product/{id}/update', 'CustomizeController@updateProduct');
// account
Route::get('/account', 'AccountController@index');
Route::post('/account/email', 'AccountController@updateEmail');
Route::post('/account/password', 'AccountController@updatePassword');
// contact us
Route::get('/contact', 'ContactUsController@index');
Route::post('/contact', 'ContactUsController@contact');
// Image route
Route::post('image/upload', 'ImageController@uploadImage');
Route::post('image/delete', 'ImageController@deleteImage');
Route::get('image/{id}/src', 'ImageController@imageSrc');

Route::group(['prefix' => 'admin'], function () {
    // dashboard
    Route::get('/', 'DashboardController@index');
    Route::post('/statistics/{range}', 'DashboardController@salesStatistics');
    // customization (step)
    Route::get('customize/step', 'CustomizeController@adminSteps');
    Route::get('customize/step/{id}', 'CustomizeController@adminStep');
    Route::post('customize/step/{id}', 'CustomizeController@adminUpdateStep');
    // customization (product)
    Route::get('customize/product', 'CustomizeController@adminProducts');
    Route::get('customize/product/create', 'CustomizeController@adminProductCreate');
    Route::post('customize/product/', 'CustomizeController@adminProductStore');
    Route::get('customize/product/{id}', 'CustomizeController@adminProduct');
    Route::get('customize/product/{id}/edit', 'CustomizeController@adminProductEdit');
    Route::post('customize/product/{id}', 'CustomizeController@adminProductUpdate');
    Route::post('customize/product/{id}/delete', 'CustomizeController@adminProductDelete');
    // customize (type)
    Route::get('customize/type', 'CustomizeController@adminTypes');
    Route::get('customize/type/{id}', 'CustomizeController@adminType');
    Route::post('customize/type/{id}', 'CustomizeController@adminTypeUpdate');
    // customize (component)
    Route::get('customize/component/{id}', 'CustomizeController@adminComponent');
    // order
    Route::get('order', 'OrderController@index');
    Route::get('order/{id}', 'OrderController@show');
    Route::post('order/{id}/cancel', 'OrderController@cancel');
    Route::post('order/{id}/fulfill', 'OrderController@fulfill');
    Route::post('shipment/{id}', 'OrderController@updateShipment');
    Route::post('shipment/{id}/delete', 'OrderController@cancelShipment');
    // customer
    Route::get('customer', 'CustomerController@index');
    Route::get('customer/{id}', 'CustomerController@show');
    // discount voucher
    Route::get('voucher', 'VoucherController@index');
    Route::get('voucher/create', 'VoucherController@create');
    Route::post('voucher', 'VoucherController@store');
    Route::get('voucher/{id}', 'VoucherController@show');
    Route::post('voucher/{id}/delete', 'VoucherController@destroy');
    // cms
    Route::get('cms', 'CmsController@index');
    Route::get('cms/{type}/create', 'CmsController@create');
    Route::post('cms/{type}', 'CmsController@store');
    Route::get('cms/{type}/{id}/edit', 'CmsController@edit');
    Route::post('cms/{type}/{id}', 'CmsController@update');
    Route::post('cms/{type}/{id}/delete', 'CmsController@delete');
    // message
    Route::get('message', 'ContactUsController@messages');
    Route::get('message/{id}', 'ContactUsController@reply');
});
//cms page
Route::get('/{slug}', 'CmsController@page');
