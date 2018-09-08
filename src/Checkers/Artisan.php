<?php

namespace PragmaRX\Health\Checkers;

use Illuminate\Support\Facades\Artisan as IlluminateArtisan;

class Artisan extends Base
{
    /**
     * Check resource.
     *
     * @return bool
     */
    public function check()
    {
        IlluminateArtisan::call($this->resource['command']['name'], $this->resource['command']['options']->toArray());

        return IlluminateArtisan::output() && preg_match("|{$this->resource['should_return']}|", IlluminateArtisan::output())
            ? $this->makeHealthyResult()
            : $this->makeResult(false, $this->resource['error_message']);
    }
}
