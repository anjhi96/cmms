<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;


use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        if (!in_array(Auth::user()->role, $roles, true)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}