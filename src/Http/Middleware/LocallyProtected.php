<?php

namespace PragmaRX\Health\Http\Middleware;

use Illuminate\Http\Request;
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
        if (! (new LocallyProtectedService())->check($request)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
