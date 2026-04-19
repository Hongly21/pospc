<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    //
    public function switch($lang)
    {
        if (in_array($lang, ['en', 'km'])) {
            session(['locale' => $lang]);
        }
        return redirect()->back();
    }
}
