<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            if (!$user->last_login_at || $user->last_login_at->diffInMinutes(now()) >= 5) {
                $user->timestamps = false;
                $user->update(['last_login_at' => now()]);
            }
        }

        return $next($request);
    }
}
