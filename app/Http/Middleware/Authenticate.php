<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            if (!JWTAuth::parseToken()->authenticate()) {
                throw new UnauthorizedHttpException('jwt-auth', 'User not found');
            }
        } catch (JWTException $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                throw new UnauthorizedHttpException('jwt-auth', 'Token expired');
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                throw new UnauthorizedHttpException('jwt-auth', 'Token invalid');
            } else {
                throw new UnauthorizedHttpException('jwt-auth', 'Token absent');
            }
        }

        return $next($request);
    }
}
