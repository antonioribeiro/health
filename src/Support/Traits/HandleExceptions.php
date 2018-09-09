<?php

namespace PragmaRX\Health\Support\Traits;

trait HandleExceptions
{
    /**
     * Handle exceptions for a call.
     *
     * @param \Closure $closure
     * @return mixed
     */
    protected function handleExceptions(\Closure $closure)
    {
        try {
            return $closure();
        } catch (\Exception $e) {
            report($e);
        }

        return false;
    }
}
