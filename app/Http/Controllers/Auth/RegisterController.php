<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Notifications\WelcomeEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    // use RegistersUsers;

    protected $redirectTo = '/account';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6'
        ]);

        if($validator->fails())
            return Response::json(['error' => true,'message' => $validator->messages()->all(),'code' => 400], 400);

        $user = User::create([
            'email' => request('email'),
            'password' => bcrypt(request('password'))
        ]);

        request()->session()->regenerate();
        Auth::guard()->login($user);

        // $user->notify(new WelcomeEmail());

        return Response::json(['error' => false, 'code' => 200], 200);
    }
}
