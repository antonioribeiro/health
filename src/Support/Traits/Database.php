<?php

namespace PragmaRX\Health\Support\Traits;

trait Database
{
    protected $database;

    public function loadDatabase()
    {
        $this->database = $this->__load();
    }

    /**
     * Load cache.
     *
     * @return \Illuminate\Support\Collection
     */
    public function __load()
    {
        if (!file_exists($file = $this->getDatabaseFileName())) {
            return collect();
        }

        return collect(json_decode(file_get_contents($file), true));
    }

    /**
     * Get cache filename.
     *
     * @return string|null
     */
    protected function getDatabaseFileName()
    {
        return $this->target->save_to;
    }
}
