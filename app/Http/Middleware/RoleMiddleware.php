<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$allowedRoles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

    
        if (!$user->role) {
            abort(403, 'Access Denied: You have no role assigned.');
        }

        $userRole = strtolower($user->role->RoleName);
        $allowedRoles = array_map('strtolower', $allowedRoles);

        if (in_array($userRole, $allowedRoles)) {
            return $next($request);
        }

        abort(403, "Unauthorized. Your role is '{$user->role->RoleName}', but this page requires: " . implode(', ', $allowedRoles));
    }
}
