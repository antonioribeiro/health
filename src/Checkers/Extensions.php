<?php

namespace PragmaRX\Health\Checkers;

use PragmaRX\Health\Support\Result;

class Extensions extends Base
{
    /**
     * Check resource.
     *
     * @return Result
     */
    public function check()
    {
        $needed = collect($this->target->items);

        $installed = collect(get_loaded_extensions());

        $alerts = $needed->reject(fn ($value) => $installed->contains($value));

        if (count($alerts) === 0) {
            return $this->makeHealthyResult();
        }

        $problems = collect($alerts)->implode(', ');

        return $this->makeResult(
            false,
            sprintf($this->target->getErrorMessage(), $problems)
        );
    }
}
