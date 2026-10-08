<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Middleware for admin check. Super simplified, ill work on it later
        if (! $request->user() || ! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized action. Admins only.');
        }
        return $next($request);
    }
}
