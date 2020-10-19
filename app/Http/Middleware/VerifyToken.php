<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VerifyToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $model
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next, string $model)
    {
        if (! (class_basename($request->user()) === Str::ucfirst($model))) {
            throw new AuthenticationException();
        }

        return $next($request);
    }
}
