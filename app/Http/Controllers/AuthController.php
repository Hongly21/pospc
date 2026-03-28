<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::with('role')->where('Email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->PasswordHash)) {

            if ($user->Status === 'Pending') {
                return back()->withErrors(['email' => 'គណនីរបស់អ្នកកំពុងរង់ចាំការយល់ព្រមពីអ្នកគ្រប់គ្រង']);
            }

            if ($user->Status === 'Reject') {
                return back()->withErrors(['email' => 'គណនីរបស់អ្នកត្រូវបានបដិសេធ']);
            }

            if ($user->Status !== 'Approved') {
                return back()->withErrors(['email' => 'គណនីរបស់អ្នកមិនត្រូវបានអនុញ្ញាតទេ សូមទាក់ទងអ្នកគ្រប់គ្រង']);
            }

            Auth::login($user);

            $request->session()->regenerate();

            $roleName = strtolower($user->role->RoleName ?? '');

            if ($roleName === 'admin' || $roleName === 'manager') {
                return redirect()->route('dashboard');
            }

            return redirect()->route('pos.index');
        }

        return back()->withErrors(['email' => 'ព័ត៌មានផ្ទៀងផ្ទាត់ដែលបានផ្តល់ឱ្យមិនត្រូវគ្នានឹងកំណត់ត្រារបស់យើងទេ']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,Email',
            'password' => 'required|min:6|confirmed'
        ]);
        
        try {
            User::create([
                'Username' => $request->username,
                'Email' => $request->email,
                'PasswordHash' => Hash::make($request->password),
                'RoleID' => 3,
                'Status' => 'Pending'
            ]);

            return redirect()->route('login')->with('success', 'ការចុះឈ្មោះបានជោគជ័យ! សូមរង់ចាំការយល់ព្រមពីអ្នកគ្រប់គ្រង');
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
