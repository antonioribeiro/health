<?php

namespace PragmaRX\Health\Support\Traits;

use Illuminate\Support\Str;

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
            $key = Str::camel($key);

            if (! property_exists($this, $key)) {
                $this->$key = $value;
            }
        });
    }
}
