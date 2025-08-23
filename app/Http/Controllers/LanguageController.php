<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($lang)
    {
        if (in_array($lang, ['en', 'fr', 'es', 'br'])) {
            App::setLocale($lang);
            Session::put('locale', $lang);
            if (auth()->check()) {
                $user = auth()->user();
                $user->locale = $lang;
                $user->save();
            }
        }
        return back();
    }
}
