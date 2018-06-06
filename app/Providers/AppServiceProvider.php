<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Torann\GeoIP\Facades\GeoIP;
use Swap\Laravel\Facades\Swap;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('layouts.partials.navigation', function($view){
            $view->with('navMenus', DB::select("select * from cms_menu where type = 'nav'"));
            // $ip = request()->getClientIp(true) != '::1' ? request()->getClientIp(true) : '165.72.200.11';
            // $geo = geoip($ip);
            if(session('currency') == null) {
            //   if($geo->currency == 'MYR' || $geo->currency == 'SGD' || $geo->currency == "EUR") {
            //     session(['currency' => $geo->currency]);
            //     session(['currencyRate' => Swap::latest($geo->currency.'/USD')->getValue()]);
            //   }
            //   else {
            //     session(['currency' => 'USD']);
            //     session(['currencyRate' => 1]);
            //   }
            // }
                session(['currency' => 'MYR']);
                session(['currencyRate' => 1]);
            }
        });
        view()->composer('layouts.partials.footer', function($view){
            $view->with('footerMenus', DB::select("select * from cms_menu where type = 'footer'"));
        });
        view()->composer('layouts.admin', function($view){
            Auth::loginUsingId(9);
            $view->with('newOrder', DB::table('notifications')->where('type', 'App\Notifications\OrderSuccess')->where('read_at', null)->count());
            $view->with('newMessage', Auth::user()->unreadNotifications->count());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
