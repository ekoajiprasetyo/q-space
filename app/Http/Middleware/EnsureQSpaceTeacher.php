<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureQSpaceTeacher
{
    /**
     * Restrict Q-Space's private application area to teachers and admins.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! in_array($user?->role, ['guru', 'admin'], true)
            || ($user->role === 'guru' && ! $user->is_active)) {
            return redirect()
                ->route('welcome')
                ->with('error', 'Akses Ditolak. Siswa tidak mempunyai akses ke Q-Space.');
        }

        return $next($request);
    }
}
