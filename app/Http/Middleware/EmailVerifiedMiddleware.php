<?php

namespace App\Http\Middleware;

use Closure;

class EmailVerifiedMiddleware
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
        $email = $request->get('email');
        if (!$email){
            abort(403);
        }
        return $next($request);
    }
}
