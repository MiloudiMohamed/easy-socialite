<?php

namespace Devmi\EasySocialite\Http\Middlewares;

use Closure;
use Illuminate\Support\Str;

class AbortIfNotActivated
{
    public function handle($request, Closure $next)
    {
        if ( ! in_array(
                Str::lower($request->provider),
                array_keys(config('easysocialite.providers'))
            )) {
            abort(404);
        }

        return $next($request);
    }
}
