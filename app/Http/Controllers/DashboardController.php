<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;
use App\CustomizeProduct;
use App\Order;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','auth.admin']);
    }

    public function index()
    {
        // total order, customer, customized product, total earm money
        $customers = User::where('role', 1);
        $products = CustomizeProduct::where('created_by', '!=', Auth::user()->id);
        $orders = Order::where('order_status', 1);
        $myr = 0;
        $sgd = 0;
        $euro = 0;
        $usd = 0;
        foreach($orders->get() as $order) {
            switch($order->currency) {
                case 'USD':
                    $usd += $order->amount();
                    break;

                case 'MYR':
                    $myr += $order->amount();
                    break;

                case 'SGD':
                    $sgd += $order->amount();
                    break;

                case 'EURO':
                    $euro += $order->amount();
                    break;

            }
        }

        return view('admin.dashboard', compact(
            'customers',
            'products',
            'orders',
            'usd',
            'sgd',
            'euro',
            'myr'
        ));
    }

    public function salesStatistics($range)
    {
        $labelsData = [];
        $salesData = [];
        $myrSales = [];
        $usdSales = [];
        $euSales = [];
        $sgdSales = [];
        for($i = $range-1; $i>=0; $i--) {
            $date = Carbon::today()->subDay($i);
            $orders = Order::whereDate('created_at', '=', $date);
            $myr = 0;
            $sgd = 0;
            $euro = 0;
            $usd = 0;
            if($orders) {
                $orderCount = $orders->count();
                foreach($orders->get() as $order) {
                    switch($order->currency) {
                        case 'USD':
                            $usd += $order->amount();
                            break;

                        case 'MYR':
                            $myr += $order->amount();
                            break;

                        case 'SGD':
                            $sgd += $order->amount();
                            break;

                        case 'EURO':
                            $euro += $order->amount();
                            break;

                    }
                }
            }
            $labelsData[] = $date->format('j M');
            $myrSales[] = round($myr, 2);
            $sgdSales[] = round($sgd, 2);
            $usdSales[] = round($usd, 2);
            $euSales[] = round($euro, 2);
        }

        return Response::json([
            'label' => $labelsData,
            'myr' => $myrSales,
            'sgd' => $sgdSales,
            'usd' => $usdSales,
            'eu' => $euSales
        ], 200);
    }
}
