<?php

namespace PragmaRX\Health\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use PragmaRX\Health\Support\LocallyProtected as LocallyProtectedService;

class LocallyProtected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle($request, $next, ...$guards)
    {
        if (!(new LocallyProtectedService())->check($request)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
