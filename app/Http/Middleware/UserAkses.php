<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserAkses
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect('/panel-admin');
        }

        if (in_array(auth()->user()->role, $roles)) {
            return $next($request);
        }

        return redirect('/error');
    }
}
