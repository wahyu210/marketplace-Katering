<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user || $user->role->value !== $role) {
            if ($user) {
                return redirect()->route($user->homeRoute());
            }
            abort(403);
        }

        return $next($request);
    }
}
