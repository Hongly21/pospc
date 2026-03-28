<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::first();

        $setting->shop_name = $request->shop_name;
        $setting->shop_phone = $request->shop_phone;
        $setting->shop_address = $request->shop_address;
        $setting->save();

        return back()->with('success', 'បានកែប្រែដោយជោគជ័យ');
    }
}
