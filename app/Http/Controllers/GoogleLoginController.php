<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $existingUser = User::where('email', $googleUser->getEmail())->first();

        if ($existingUser && $existingUser->google_id === null) {
            $existingUser->update(['google_id' => $googleUser->id]);
            Auth::login($existingUser);
        } else {
            $user = User::updateOrCreate(
                ['google_id' => $googleUser->id],
                [
                    'email' => $googleUser->getEmail(),
                    'name' => $googleUser->getName(),
                    'password' => Hash::make(Str::password()),
                ]
            );

            Auth::login($user);
        }

        return redirect()->intended(route('home', absolute: false));
    }
}
