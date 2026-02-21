<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SitePassword
{
    public function handle(Request $request, Closure $next)
    {
        $password = config('app.password');

        if (empty($password)) {
            return $next($request);
        }

        if ($request->session()->get('site_authenticated')) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
