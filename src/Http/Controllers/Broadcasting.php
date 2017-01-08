<?php

namespace PragmaRX\Health\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Broadcasting extends Controller
{
    public function callback($secret, Request $request)
    {
        $resource = json_decode($request->get('data'), true)['resource'];

        $checker = $this->instantiateChecker($resource);

        $checker->pong($secret);
    }

    /**
     * @param $resource
     * @return mixed
     */
    protected function instantiateChecker($resource)
    {
        $checkerClass = $resource['checker'];

        $checker = new $checkerClass($resource, []);

        return $checker;
    }
}
