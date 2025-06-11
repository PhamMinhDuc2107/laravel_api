<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $now = Carbon::now();
        $start = Carbon::create(7,0);
        $end = Carbon::create(17,0);
        if(!$now->between($start,$end)){
            return response()->json(['message' => 'Server is not available', "status" => 403], 503);
        }
        return $next($request);
    }
}
