<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Throwable;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
        ->scopes(['openid', 'https://www.googleapis.com/auth/userinfo.profile', 'https://www.googleapis.com/auth/userinfo.email'])
        ->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return void
     */
    public function handleGoogleCallback(Request $request)
    {
        // try {
        //     $googleUser = Socialite::driver('google')->user();
        // } catch (Throwable $e) {
        //     return redirect()->route('login')->with('error', 'Google authentication failed.');
        // }
        $googleUser = Socialite::driver('google')->stateless()->user();
        
        $user = User::updateOrCreate(
            ['google_id' => $googleUser->id],
            [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => bcrypt(Str::random(16)),
            ]
        );

        // Login the user
        Auth::login($user, true); // Remember the user

        return Redirect::to('/');
    }

    /**
     * Login with Facebook.
     *
     * @return void
     */
    public function redirectToFacebookProvider() 
    { 
        return Socialite::driver('facebook')->redirect(); 
    } 
    
    /**
     * Login with Facebook callback.
     *
     * @return void
     */
    public function handleFacebookProviderCallback() 
    { 
        try {
            $facebookUser = Socialite::driver('facebook')->user();
        } catch (Throwable $e) {
            return redirect()->route('login')->with('error', 'Facebook authentication failed.');
        }
        
        $user = User::firstOrCreate(
            [
                'name' => $facebookUser->name,
                'email' => $facebookUser->email,
                'password' => bcrypt(Str::random(16)),
            ]
        );

        // Login the user
        Auth::login($user, true); // Remember the user

        return Redirect::to('/');
    }
}
