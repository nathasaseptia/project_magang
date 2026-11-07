<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles, true)) {
            if ($user->isSuperAdmin()) {
                return redirect()->route('superadmin.akun');
            }
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
