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
        $amount = 0;
        foreach($orders->get() as $order)
            $amount += $order->amount();

        return view('admin.dashboard', compact(
            'customers',
            'products',
            'orders',
            'amount',
            'notifications'
        ));
    }
}
