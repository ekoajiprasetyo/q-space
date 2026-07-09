<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserGoogleToken;
use Google\Service\Drive;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes([
                Drive::DRIVE_FILE,
                Drive::DRIVE_METADATA,
            ])
            ->with(['access_type' => 'offline', 'prompt' => 'consent select_account'])
            ->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Scenario 1: User is already logged in (Linking Account Context)
            if (Auth::check()) {
                $user = Auth::user();

                // Prepare token data, only update refresh_token if provided
                $tokenData = [
                    'google_email' => $googleUser->email,
                    'google_name' => $googleUser->name,
                    'google_avatar' => $googleUser->avatar,
                    'access_token' => $googleUser->token,
                    'expires_at' => now()->addSeconds($googleUser->expiresIn),
                ];
                
                if ($googleUser->refreshToken) {
                    $tokenData['refresh_token'] = $googleUser->refreshToken;
                }

                // Save or Update the token for the CURRENT user
                UserGoogleToken::updateOrCreate(
                    ['user_id' => $user->id],
                    $tokenData
                );

                // Optional: Link google_id if it's missing (for reference)
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }

                if ($user->role === 'siswa') {
                    return redirect()->route('welcome')->with('error', 'Akses Ditolak. Siswa tidak mempunyai akses ke halaman dashboard.');
                }

                return redirect()->intended('dashboard');
            }

            // Scenario 2: Guest User (Login/Register Context)
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                if (!config('app.auth_bridge.allow_google_user_autocreate', true)) {
                    return redirect()->route('login')->with(
                        'error',
                        'Akun Anda belum tersedia di Q-Space. Silakan gunakan atau sinkronkan akun dari Q-Link terlebih dahulu.'
                    );
                }

                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => bcrypt(str()->random(24)),
                    'google_id' => $googleUser->id,
                    'email_verified_at' => now(), // Mark as verified immediately
                ]);
            } else {
                // Update google_id if missing
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
                // Ensure verified if they are using Google Login
                if (!$user->email_verified_at) {
                    $user->update(['email_verified_at' => now()]);
                }
            }

            if ((bool) $user->is_active === false) {
                Auth::logout();

                return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan. Hubungi admin Q-Link.');
            }

            Auth::login($user);

            // Prepare token data, only update refresh_token if provided
            $tokenData = [
                'google_email' => $googleUser->email,
                'google_name' => $googleUser->name,
                'google_avatar' => $googleUser->avatar,
                'access_token' => $googleUser->token,
                'expires_at' => now()->addSeconds($googleUser->expiresIn),
            ];
            
            if ($googleUser->refreshToken) {
                $tokenData['refresh_token'] = $googleUser->refreshToken;
            }

            UserGoogleToken::updateOrCreate(
                ['user_id' => $user->id],
                $tokenData
            );

            if ($user->role === 'siswa') {
                return redirect()->route('welcome')->with('error', 'Akses Ditolak. Siswa tidak mempunyai akses ke halaman dashboard.');
            }

            return redirect()->intended('dashboard');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login Failed: ' . $e->getMessage());
        }
    }
}
