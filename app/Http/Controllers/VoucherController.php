<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Voucher;

class VoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','auth.admin']);
    }

    public function index()
    {
        $vouchers = Voucher::all();
        return view('admin.voucher.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.voucher.create');
    }

    public function show($id)
    {
        $voucher = Voucher::where('id', $id)->first();
        if(!$voucher) abort('404');
        return view('admin.voucher.show', compact('voucher'));
    }

}
