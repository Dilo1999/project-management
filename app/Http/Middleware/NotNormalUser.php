<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NotNormalUser
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && method_exists($user, 'isNormal') && $user->isNormal()) {
            return redirect()->route('book-ticket')
                ->with('error', 'You do not have access to that page.');
        }

        return $next($request);
    }
}

