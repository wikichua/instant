<?php

namespace Wikichua\Instant\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate;

class AuthAdmin extends Authenticate
{
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
