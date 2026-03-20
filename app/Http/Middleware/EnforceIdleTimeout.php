<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceIdleTimeout
{
    public const SESSION_KEY = 'last_activity_at';

    /**
     * Log out users who have been inactive longer than session.lifetime (minutes).
     * Also clears "remember me" so idle rules apply to everyone.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $lifetimeSeconds = max(1, (int) config('session.lifetime', 60)) * 60;
        $last = $request->session()->get(self::SESSION_KEY);

        if (is_numeric($last) && (time() - (int) $last) >= $lifetimeSeconds) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('idle_message', __('You were logged out due to inactivity. Please sign in again.'));
        }

        $request->session()->put(self::SESSION_KEY, time());

        return $next($request);
    }
}
