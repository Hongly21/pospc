<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }


    public function update(Request $request)
    {
        $user = User::find(Auth::id());

        $request->merge([
            'username' => trim((string) $request->input('username')),
            'email' => strtolower(trim((string) $request->input('email'))),
            'phone' => $request->filled('phone') ? trim((string) $request->input('phone')) : null,
        ]);

        $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'Username')->ignore($user->UserID, 'UserID')],
            'email' => ['required', 'email', Rule::unique('users', 'Email')->ignore($user->UserID, 'UserID')],
            'phone' => 'nullable|string|max:20',
            'user_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->Username = $request->username;
        $user->Email = $request->email;

        if ($request->has('phone')) {
            $user->PhoneNumber = $request->phone;
        }


        if ($request->hasFile('user_image')) {
            if ($user->UserImage && Storage::disk('public')->exists($user->UserImage)) {
                Storage::disk('public')->delete($user->UserImage);
            }

            $file = $request->file('user_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('profile_images', $filename, 'public');

            $user->UserImage = $path;
        }

        if ($request->filled('password')) {
            $request->validate([
                'password' => [
                    'confirmed',
                    Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
                ],
            ]);
            $user->PasswordHash = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', __('profile.msg_updated'));
    }
}
