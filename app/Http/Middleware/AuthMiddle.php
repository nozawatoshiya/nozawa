<?php

namespace App\Http\Middleware;

use Closure;

class AuthMiddle
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
        if(! \Session::get('Authenticated')){
          return redirect('/');
        }
          return $next($request);
    }
}
