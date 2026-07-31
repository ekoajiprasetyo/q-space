<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PostgresSchema;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View|RedirectResponse
    {
        if (PostgresSchema::usesPgsql() || ! config('app.auth_bridge.allow_local_registration', true)) {
            return redirect()->away($this->masterRegistrationUrl(request()));
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if (PostgresSchema::usesPgsql() || ! config('app.auth_bridge.allow_local_registration', true)) {
            return redirect()->away($this->masterRegistrationUrl($request));
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Build the Q-Link registration URL while retaining Q-Space as the
     * post-registration destination for teacher accounts.
     */
    private function masterRegistrationUrl(Request $request): string
    {
        $dashboardUrl = $request->getSchemeAndHttpHost().'/dashboard';

        return config('app.q_link_master_url').'/register?'.http_build_query([
            'role' => 'guru',
            'redirect' => $dashboardUrl,
        ]);
    }
}
