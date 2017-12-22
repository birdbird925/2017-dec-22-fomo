<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','auth.admin']);
    }
    
    public function index()
    {
        $customers = User::where('role', 1)->get();
        return view('admin.customer.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = User::where('id', $id)->where('role', 1)->first();
        if(!$customer) abort('404');
        return view('admin.customer.show', compact('customer'));
    }

}
