<?php

namespace Wikichua\Instant\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;

class ReAuth
{
    public function handle($request, Closure $next)
    {
        if ($this->lastAuth($request) > config('sap.reauth.timeout', 3600)) {
            if ($request->ajax()) {
                $request->session()->put(str_slug(strtolower(config('app.name'))).'.reauth.requested_url', back()->getTargetUrl());

                return Redirect::route('reauth');
            }
            $request->session()->put(str_slug(strtolower(config('app.name'))).'.reauth.requested_url', $request->fullUrl());

            return redirect()->route('reauth');
        }
        if (config('sap.reauth.reset', true)) {
            $request->session()->put(str_slug(strtolower(config('app.name'))).'.reauth.last_auth', strtotime('now'));
        }

        return $next($request);
    }

    private function lastAuth($request)
    {
        return time() - $request->session()->get(str_slug(strtolower(config('app.name'))).'.reauth.last_auth', 0);
    }
}
