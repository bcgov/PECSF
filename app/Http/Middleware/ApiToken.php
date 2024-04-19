<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->api_token != env('API_TOKEN_KEY')) {
           
            Log::error("ApiToken Unauthorized found : {$request->api_token} ");
            return response()->json('Unauthorized', 401);
        } 

        return $next($request);
    }
}
