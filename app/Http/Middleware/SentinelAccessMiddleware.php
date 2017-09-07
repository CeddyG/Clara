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
    public function handle($request, Closure $next, $sType = 'web')
    {
        if (Sentinel::check())
        {
            // User is logged in and assigned to the `$user` variable.
            $action = Route::getCurrentRoute()->getName();
            
            if (Sentinel::hasAccess($action))
            {
                return $next($request);
            }
            else 
            {
                if ($sType == 'api')
                {
                    return response()->json([
                        'status'    => 403,
                        'message'   => 'Access denied'
                    ]);
                }
                else
                {
                    return response('Access denied');
                }
            }
        }
        else
        {
            // User is not logged in
            if ($sType == 'api')
            {
                return response()->json([
                    'status'    => 401,
                    'message'   => 'Vous devez être connecté et avoir les droits'
                ]);
            }
            else
            {
                return redirect('login');
            }
        }
        
    }
}
