<?php

namespace PragmaRX\Health\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LocallyProtected
{
    /**
     * Check if the request is authorized.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function check($request)
    {
        $token = $request->header('API-Token');

        try {
            $cacheKey = decrypt($token);
        } catch (\Throwable $exception) {
            return false;
        }

        $value = Cache::get($cacheKey);

        return $value === $cacheKey;
    }

    public function protect($timeout)
    {
        $cacheKey = Constants::SERVER_VARS_CACHE_KEY_PREFIX.'-'.Str::random();

        Cache::put($cacheKey, $cacheKey, $timeout ?? 60);

        return encrypt($cacheKey);
    }
}
