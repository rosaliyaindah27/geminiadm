<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableCsrfForTesting
{
    public function handle(Request $request, Closure $next): Response
    {
        // Disable CSRF for testing environment
        if (app()->environment('testing')) {
            $request->session()->regenerateToken();
        }
        
        return $next($request);
    }
}