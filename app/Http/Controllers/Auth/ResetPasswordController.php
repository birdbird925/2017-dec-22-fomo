<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/account';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function customReset(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        if($validator->fails())
            return Response::json(['error' => true,'message' => $validator->messages()->all(), 'code' => 400], 400);


        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        if($response == Password::PASSWORD_RESET)
            return Response::json(['error' => false, 'message' => trans($response), 'code' => 200], 200);
        else
            return Response::json(['error' => true, 'message' => trans($response), 'code' => 400], 400);
    }
}
