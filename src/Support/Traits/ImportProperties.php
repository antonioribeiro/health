<?php

namespace PragmaRX\Health\Support\Traits;

trait ImportProperties
{
    /**
     * Import all collection items to object properties.
     *
     * @param $data
     */
    public function importProperties($data)
    {
        $data->each(function ($value, $key) {
            $key = camel_case($key);

            if (! property_exists($this, $key)) {
                $this->$key = $value;
            }
        });
    }
}
