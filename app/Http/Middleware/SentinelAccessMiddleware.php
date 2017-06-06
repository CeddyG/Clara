<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Route;
use Closure;

use Sentinel;

class SentinelAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $action = Route::getCurrentRoute()->getName();
            
        if (Sentinel::hasAccess($action))
        {
            return $next($request);
        }
        else 
        {
            return redirect()->back();
        }
    }
}
