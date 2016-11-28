<?php

namespace PragmaRX\Health\Checkers;

interface Contract
{
    /**
     * @param $resources
     * @return mixed
     */
    public function check();

    /**
     * @param $resources
     * @return mixed
     */
    public function healthy($resources);

    /**
     * @param $resources
     * @return mixed
     */
    public function message($resources);
}
