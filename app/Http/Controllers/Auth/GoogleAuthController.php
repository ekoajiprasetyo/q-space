<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserGoogleToken;
use Google\Service\Drive;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = $request->user();

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

            return redirect()->route('dashboard')->with('success', 'Google Drive berhasil dihubungkan.');

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Gagal menghubungkan Google Drive: '.$e->getMessage());
        }
    }
}
