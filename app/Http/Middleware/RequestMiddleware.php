<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::withContext(['request_id' => uniqid('', true)]);

        return $next($request);
    }
}
