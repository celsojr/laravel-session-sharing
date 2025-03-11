<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLargeCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Create a large cookie for testing (8KB limit for most browsers)
        $largeValue = str_repeat('A', 4000); // 4KB
        $response->cookie('large_cookie', $largeValue, 120, '/', env('SESSION_DOMAIN'), true, true);

        return $response;
    }
}
