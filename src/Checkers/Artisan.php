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
        return $this->executeAndCheck()
            ? $this->makeHealthyResult()
            : $this->makeResult(false, $this->target->getErrorMessage());
    }

    /**
     * @return bool
     */
    protected function executeAndCheck()
    {
        $this->executeArtisan();

        return $this->checkArtisanOutput();
    }

    /**
     * @return bool
     */
    protected function checkArtisanOutput()
    {
        $output = IlluminateArtisan::output();

        return
            $output && preg_match("|{$this->target->shouldReturn}|", $output);
    }

    protected function executeArtisan()
    {
        IlluminateArtisan::call(
            $this->target->command['name'],
            $this->target->command['options']->toArray()
        );
    }
}
