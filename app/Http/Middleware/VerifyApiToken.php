<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class VerifyApiToken
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (!$request->has('access_token')) {
            return response()->json(['status' => 'error', 'message' => 'access token required.']);
        }
        if (!Auth::guard($guard)->check()) {
            return response()->json(['status' => 'error', 'message' => 'token mismatch.']);
        }
        return $next($request);
    }
}
