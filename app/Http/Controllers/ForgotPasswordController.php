<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\EmailOtp;
use App\Mail\OTPMail;
use Carbon\Carbon;


class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,Email']);

        $user = User::where('Email', $request->email)->first();

        $otp = rand(100000, 999999);

        EmailOtp::create([
            'UserID'     => $user->UserID,
            'email'      => $request->email,
            'otp'        => $otp,
            'expires_at' => Carbon::now()->addMinutes(15),
            'used'       => 0
        ]);

        Mail::to($request->email)->send(new OTPMail($otp));

        return redirect()->route('password.reset.form', ['email' => $request->email])
                         ->with('success', 'OTP sent! Please check your email.');
    }

    public function showResetForm(Request $request)
    {
        $email = $request->query('email');
        return view('auth.reset-password', compact('email'));
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,Email',
            'otp'      => 'required|numeric',
            'password' => 'required|min:6|confirmed'
        ]);

        $record = EmailOtp::where('email', $request->email)
                          ->where('otp', $request->otp)
                          ->where('used', 0)
                          ->where('expires_at', '>', Carbon::now())
                          ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP code.']);
        }

        $user = User::where('Email', $request->email)->first();
        $user->PasswordHash = Hash::make($request->password);
        $user->save();


        $record->used = 1;
        $record->save();

        return redirect()->route('login')->with('success', 'Password reset successful! You can now login.');
    }
}
