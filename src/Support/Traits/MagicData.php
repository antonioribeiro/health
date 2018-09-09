<?php

namespace PragmaRX\Health\Support\Traits;

trait MagicData
{
    /**
     * Magic getters for $data.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }
}
