<?php

namespace PragmaRX\Health\Checkers;

use Exception;
use PragmaRX\Health\Support\Result;

class SecurityChecker extends Base
{
    /**
     * Check resource.
     *
     * @return Result
     */
    public function check()
    {
        exec($this->getCommand(), $output);

        $alerts = collect(json_decode(collect($output)->join(''), true))->keys();

        if (count($alerts) === 0) {
            return $this->makeHealthyResult();
        }

        $problems = collect($alerts)->implode(', ');

        return $this->makeResult(
            false,
            sprintf($this->target->getErrorMessage(), $problems)
        );
    }

    public function getCommand()
    {
        if (! file_exists($executable = $this->target->resource->executable)) {
            throw new Exception("The security checker executable was not found: $executable");
        }

        return $executable.' -format json '.base_path('composer.lock');
    }
}
