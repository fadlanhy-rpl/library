<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('login'); // Or abort(401) for APIs
        }

        $user = Auth::user();

        if ($user->role !== $role) {
            // Redirect to a 'home' or 'dashboard' or throw an error
            // For APIs, abort(403, 'Unauthorized action.') is better.
            // For web, redirecting or showing a generic error page is common.
            return redirect('/dashboard')->with('error', 'You do not have permission to access this page.');
            // Or: abort(403, 'FORBIDDEN: You do not have the required role.');
        }

        return $next($request);
    }
}