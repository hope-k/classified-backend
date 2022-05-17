<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetRequest;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use App\Notifications\ForgotPasswordNotification;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    //
    public function register(RegisterRequest $req)
    {

        User::create([
            'email' => $req->email,
            'firstname' => $req->firstname,
            'lastname' => $req->lastname,
            'password' => Hash::make($req->password),
        ]);


        return $this->sendResponse('User Registered Successfully');
    }

    public function login(LoginRequest $request)
    {
        

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->sendError('Invalid Credentials', 401);
        }
        $accessToken = Auth::user()->createToken('access-token')->plainTextToken;
        $accessTokenCookie = cookie('access-token', $accessToken, 1440, null, null, false, true, false, 'none');

        return $this->sendResponse('Logged In Successfully')->withCookie($accessTokenCookie);
    }

    public function user()
    {
        if (!Auth::user()) {
            $this->sendError('', 401);
        }
        return $this->sendResponse('', ['user' => Auth::user()], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return $this->sendResponse();
    }

    public function forgot(ForgotRequest $request)
    {
       
        $resetToken = Str::random(32);
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $resetToken
        ]);


        $url = env('FRONTEND_URL') . "/{$resetToken}";
        Mail::to($request->email)->send(new ForgotPasswordMail($url));

        return $this->sendResponse('Check your email');
    }


    public function reset(ResetRequest $request, $resetToken)
    {
       

        $passwordReset = DB::table('password_resets')->where('token', $resetToken)->first();
        if (!$passwordReset) {
            return $this->sendError('Invalid reset token');
        }

        $user = User::where('email', $passwordReset->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        return $this->sendResponse('Password reset was successful');
    }

    public function sendVerifyEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->sendResponse('Email already verified!');
        }
        $request->user()->sendEmailVerificationNotification();
        return $this->sendResponse('Check your inbox for email verification link!');
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        if($request->user()->hasVerifiedEmail()){
            return $this->sendResponse('Email already verified!');
        }
        $request->user()->markEmailAsVerified();
        return $this->sendResponse('Email verified successfully!');
        
    }
}
