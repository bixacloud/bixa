<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            $user = User::where('social_id', $socialUser->getId())
                       ->where('social_type', $provider)
                       ->first();

            if (!$user) {
                $user = User::where('email', $socialUser->getEmail())->first();
                
                  if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'social_id' => $socialUser->getId(),
                    'social_type' => $provider,
                    'avatar' => 'avatar-1.jpg',
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => null 
                ]);
                
                $user->sendEmailVerificationNotification();
            }
				
				else {
                    $user->update([
                        'social_id' => $socialUser->getId(),
                        'social_type' => $provider,
                    ]);
                }
            }

            Auth::login($user);
            return redirect()->intended('dashboard');

        } catch (Exception $e) {
            return redirect()->route('login')
                           ->with('error', 'Unable to login with ' . ucfirst($provider));
        }
    }
}