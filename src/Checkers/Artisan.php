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
    protected function executeAndCheck(): bool
    {
        $this->executeArtisan();

        return $this->checkArtisanOutput();
    }

    /**
     * @return bool
     */
    protected function checkArtisanOutput(): bool
    {
        $output = IlluminateArtisan::output();

        return
            $output && preg_match("|{$this->target->should_return}|", $output);
    }

    protected function executeArtisan(): void
    {
        IlluminateArtisan::call(
            $this->target->command['name'],
            $this->target->command['options']->toArray()
        );
    }
}
