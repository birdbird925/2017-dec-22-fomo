<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use App\Address;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(Auth::user()->role == 1)
            return view('account');
        else
            return view('admin.account');
    }

    public function updateEmail()
    {
        $validator = Validator::make(request()->all(), ['email' => 'required|email|max:255|unique:users,email,'.Auth::user()->id.',id',]);
        if($validator->fails()) {
            if(request('submitBy') == 'js')
                return Response::json(['error' => true,'message' => $validator->messages()->all(),'code' => 400], 400);
            else
                return redirect()->back()->withErrors($validator->messages());
        }

        Auth::user()->update(['email' => request('email')]);

        if(request('submitBy') != 'js')
            return redirect('/account');
    }

    public function updatePassword()
    {
        if(request('submitBy') != 'js')
            $rule = ['Old_Password' => 'required|min:6', 'New_Password' => 'required|min:6'];
        else
            $rule = ['New_Password' => 'required|min:6'];

        $validator = Validator::make(request()->all(), $rule);
        if($validator->fails()) {
            if(request('submitBy') == 'js')
                return Response::json(['error' => true,'message' => $validator->messages()->all(),'code' => 400], 400);
            else
                return redirect()->back()->withErrors($validator->messages());
        }

        if(Auth::user()->password != '') {
            if(!Hash::check(request('Old_Password'), Auth::user()->password))
                if(request('submitBy') == 'js')
                    return Response::json(['error' => true,'message' => 'Incorrect old password','code' => 400], 400);
                else
                    return redirect()->back()->withErrors(['Old_Password' => 'Incorrect old password']);
        }

        Auth::user()->update([
            'password' => bcrypt(request('New_Password'))
        ]);

        if(request('submitBy') != 'js')
            return redirect('/account');
    }
}
