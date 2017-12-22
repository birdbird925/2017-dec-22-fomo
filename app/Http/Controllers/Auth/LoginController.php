<?php

namespace App\Http\Controllers\Auth;


use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use App\Services\SocialAccountService;

class LoginController extends Controller
{
    // use AuthenticatesUsers;

    protected $redirectTo = '/account';

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function login()
    {
        // validation login
        $validator = Validator::make(request()->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if($validator->fails())
            return Response::json(['error' => true,'message' => $validator->messages()->all(), 'code' => 400], 400);

        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            request()->session()->regenerate();
            return Response::json(['error' => false, 'code' => 200], 200);
        }
        else {
            return Response::json(['error' => true,'message' => 'Email and password do not match.', 'code' => 400], 400);
        }
    }

    public function logout()
    {
        Auth::guard()->logout();
        request()->session()->flush();
        request()->session()->regenerate();
        return redirect('/');
    }

    public function redirectToProvider($socialProvider)
    {
       session(['url.intended' => request('backto')]);
        return Socialite::driver($socialProvider)->redirect();
    }

    public function handleProviderCallback(SocialAccountService $service, $socialProvider)
    {
      $user = $service->createOrGetUser(Socialite::driver($socialProvider)->user(), $socialProvider);
      auth()->login($user);
      return redirect()->intended();
    }
}
