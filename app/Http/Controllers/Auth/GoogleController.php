<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException|\Exception $e) {
            return redirect()->route('login')->withErrors([
                'google' => 'Google sign-in failed. Please try again.',
            ]);
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            $user = new User();
            $user->email = $googleUser->getEmail();
            $user->name = $googleUser->getName() ?? $googleUser->getNickname() ?? $googleUser->getEmail();
            $user->password = Hash::make(Str::random(32));
            $user->email_verified_at = now();
            $user->save();
        } elseif (! $user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
        }

        Auth::login($user);

        return redirect()->intended(route('dashboard'));
    }
}
