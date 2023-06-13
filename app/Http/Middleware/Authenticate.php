<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Str;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // if (! $request->expectsJson()) {
        //     return route('seller.login.post');
        // }

        if (! $request->expectsJson()) {

            $uri = $request->path();

            // URIが以下３つから始まる場合
            if(Str::startsWith($uri, ['sellers/'])) {

                return 'seller-login';

            }

            return route('login');
        }
    }
}
