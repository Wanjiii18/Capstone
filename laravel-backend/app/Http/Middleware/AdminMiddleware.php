<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Check if user has admin role
        $user = auth()->user();
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin privileges required.',
                'error' => 'Insufficient permissions'
            ], 403);
        }

        return $next($request);
    }
}
