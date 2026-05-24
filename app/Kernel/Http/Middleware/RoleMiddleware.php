<?php

namespace App\Kernel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }
            return redirect()->route('login');
        }

        // Check roles
        foreach ($roles as $role) {
            if ($user->hasRole($role) || $user->role === $role) {
                return $next($request);
            }
        }

        // Check permissions (prefixed with 'permission:')
        foreach ($roles as $role) {
            if (str_starts_with($role, 'permission:')) {
                $permission = substr($role, strlen('permission:'));
                if ($user->can($permission)) {
                    return $next($request);
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: insufficient role/permission',
            ], 403);
        }

        abort(403, 'Forbidden: insufficient role/permission');
    }
}
