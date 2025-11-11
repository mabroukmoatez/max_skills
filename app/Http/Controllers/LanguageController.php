<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class LanguageController extends Controller
{
    public function setLanguage($locale)
    {
        if (! in_array($locale, ['en', 'fr'])) {
            abort(400);
        }
    
        app()->setLocale($locale);
        Session::put('locale', $locale);
 
        if (Auth::check()) {
            $user = Auth::user();
            $user->language = $locale;
            $user->save();
         }
        return redirect()->back();
    }
}
