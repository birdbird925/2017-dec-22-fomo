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

    public function store(Request $request)
    {
        $rule = [
          'name' => 'required|max:255',
          'code' => 'required|unique:voucher,code|max:12',
          'start_time' => 'required|date|after:yesterday',
          'end_time' => 'required|date|after:start_date',
          'quantity' => 'required|integer',
        ];
        if($request->type == '1')
          $rule['discount'] = 'required|between:1,99';

        if($request->type == '1')
          $rule['discount'] = 'required|integer';

        $this->validate($request, $rule);

        Voucher::create([
          'name' => $request->name,
          'code' => $request->code,
          'type' => $request->type,
          'value' => $request->type != 3 ? $request->discount : null,
          'start_at' => date_create_from_format('m/d/Y', $request->start_time),
          'expired_at' => date_create_from_format('m/d/Y', $request->end_time),
          'quantity' => $request->quantity
        ]);

        return redirect('/admin/voucher');
    }

    public function show($id)
    {
        $voucher = Voucher::where('id', $id)->first();
        if(!$voucher) abort('404');
        return view('admin.voucher.show', compact('voucher'));
    }

}
