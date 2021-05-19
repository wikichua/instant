<?php

namespace Wikichua\Instant\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request)
    {
        return parent::version($request);
    }

    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
                'avatar' => 'https://ui-avatars.com/api/?name=' . ($request->user()->name ?? '')
            ],
            'brand' => config('app.name'),
            'status' => $request->session()->pull('status'),
            'flash' => $request->session()->pull('flash'),
            'impersonating' => app('impersonate')->isImpersonating(),
        ]);
    }
}
