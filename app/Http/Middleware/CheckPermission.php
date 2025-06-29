<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, String $permission): Response
    {
        if (!$permission) {
            return $next($request);
        }

        if (!auth()->user()->can($permission)) {
            return back()->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
