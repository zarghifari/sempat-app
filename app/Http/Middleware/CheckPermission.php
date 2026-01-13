<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }
            return redirect()->route('login');
        }

        // Check if user has any of the required permissions
        foreach ($permissions as $permission) {
            if ($request->user()->hasPermission($permission)) {
                return $next($request);
            }
        }

        // User doesn't have any of the required permissions
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Forbidden. You do not have the required permission.',
                'required_permissions' => $permissions,
            ], 403);
        }
        
        abort(403, 'You do not have permission to perform this action.');
    }
}
