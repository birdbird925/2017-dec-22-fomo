<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Password;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function customSendResetLinkEmail(Request $request)
    {
        $validator = Validator::make(request()->all(), ['email' => 'required|email']);

        if($validator->fails())
            return Response::json(['error' => true,'message' => $validator->messages()->all(), 'code' => 400], 400);

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if($response == Password::RESET_LINK_SENT)
            return Response::json(['error' => false, 'message' => trans($response), 'code' => 200], 200);
        else
            return Response::json(['error' => true, 'message' => trans($response), 'code' => 400], 400);
    }
}
