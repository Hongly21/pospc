<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use Illuminate\Validation\Rules\Password; // <-- Added this import!
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Username', 'LIKE', "%{$search}%")
                    ->orWhere('Email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('RoleID', $request->role_id);
        }

        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        $users = $query->orderBy('UserID', 'desc')->paginate(15)->appends($request->query());

        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function approve($id)
    {
        $user = User::where('UserID', $id)->firstOrFail();

        $user->Status = 'Approved';
        $user->ActionBy = Auth::user()->UserID;
        $user->ActionAt = now();

        $user->save();

        return redirect()->back()->with('success', __('users.msg_approved'));
    }

    public function reject($id)
    {
        $user = User::where('UserID', $id)->firstOrFail();

        $user->Status = 'Reject';
        $user->ActionBy = Auth::user()->UserID;
        $user->ActionAt = now();
        $user->save();

        return redirect()->back()->with('success', __('users.msg_rejected'));
    }

    public function update(Request $request)
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
        ]);

        $request->validate([
            'id' => 'required',
            'name' => ['required', 'string', 'max:50', Rule::unique('users', 'Username')->ignore($request->id, 'UserID')],
            'role_id' => 'required|exists:roles,RoleID',
            'password' => [
                'nullable', // Optional during update
                Password::min(8) // But must be strong if provided!
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ], [
            'name.unique' => 'Username already exists.',
        ]);

        $user = User::where('UserID', $request->id)->firstOrFail();

        $user->Username = $request->name;
        $user->RoleID = $request->role_id;

        if ($request->filled('password')) {
            $user->PasswordHash = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', __('users.msg_updated'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function store(Request $request)
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('users', 'Username')],
            'email' => ['required', 'email', Rule::unique('users', 'Email')],
            'role' => 'required',
            'password' => [
                'required',
                'confirmed', // Checks against password_confirmation from the view
                Password::min(8) // Must be at least 8 chars long
                    ->letters()  // Must have letters
                    ->mixedCase() // Must have Upper and Lower case
                    ->numbers()   // Must have numbers
                    ->symbols()   // Must have symbols (@, #, $, etc.)
            ],
        ], [
            'name.unique' => 'Username already exists.',
            'email.unique' => 'Email already exists.',
        ]);

        $user = new User();
        $user->Username = $request->name;
        $user->Email = $request->email;
        $user->PasswordHash = Hash::make($request->password);
        $user->RoleID = $request->role;
        $user->Status = 'Approved';
        $user->save();

        return response()->json(['status' => 'success']);
    }

    public function destroy(Request $request)
    {
        try {
            User::destroy($request->id);
            return response()->json('success');
        } catch (\Exception $e) {
            return response()->json('error');
        }
    }
}
