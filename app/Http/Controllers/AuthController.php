<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password; // <-- 1. Added this import!
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

public function login(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::with('role')->where('Email', $request->email)->first();

        // ONLY triggers if the password is correct
        if ($user && Hash::check($request->password, $user->PasswordHash)) {

            if ($user->Status === 'Pending') {
                return back()->withErrors(['email' => __('auth.account_pending')]);
            }

            if ($user->Status === 'Reject') {
                return back()->withErrors(['email' => __('auth.account_rejected')]);
            }

            if ($user->Status !== 'Approved') {
                return back()->withErrors(['email' => __('auth.account_unauthorized')]);
            }

            Auth::login($user);
            $request->session()->regenerate();

            $roleName = strtolower($user->role->RoleName ?? '');

            // Determine where the user should go
            $targetUrl = ($roleName === 'admin' || $roleName === 'manager')
                            ? route('dashboard')
                            : route('pos.index');

            // Return the login view with a success flag to trigger the SweetAlert
            return view('auth.login', [
                'login_success' => true,
                'redirect_url' => $targetUrl
            ]);
        }

        // If password is wrong, return normal error
        return back()->withErrors(['email' => __('auth.invalid_credentials')]);
    }

    public function register(Request $request)
    {
        $request->merge([
            'username' => trim((string) $request->input('username')),
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        // 2. Updated the validation array here!
        $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'Username')],
            'email' => ['required', 'email', Rule::unique('users', 'Email')],
            'password' => [
                'required',
                'confirmed', // This checks that it matches the password_confirmation input
                Password::min(8) // Must be at least 8 characters
                    ->letters()  // Must contain letters
                    ->mixedCase()// Must contain uppercase and lowercase
                    ->numbers()  // Must contain numbers
                    ->symbols()  // Must contain symbols (like @, #, !, etc.)
            ]
        ]);

        try {
            User::create([
                'Username' => $request->username,
                'Email' => $request->email,
                'PasswordHash' => Hash::make($request->password),
                'RoleID' => 3, // Default role for new registrations
                'Status' => 'Pending'
            ]);

            return redirect()->route('login')->with('success', __('auth.registration_success'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
