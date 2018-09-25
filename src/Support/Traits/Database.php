<?php

namespace PragmaRX\Health\Support\Traits;

use PragmaRX\Health\Data\Models\HealthCheck;

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
        if (! file_exists($file = $this->getDatabaseFileName())) {
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
        return $this->target->saveTo;
    }

    /**
     * Check if database is enabled.
     *
     * @return bool
     */
    protected function databaseEnabled()
    {
        return config('health.database.enabled');
    }

    protected function saveResultsToDatabase($target, $result)
    {
        HealthCheck::create([
            'resource_name' => $resource = $target->resource->name,
            'resource_slug' => $target->resource->slug,
            'target_name' => $target->name,
            'target_slug' => str_slug($target->name),
            'target_display' => $target->display,
            'healthy' => $result->healthy,
            'error_message' => $result->errorMessage,
            'runtime' => $result->elapsedTime,
            'value' => $result->value,
            'value_human' => $result->valueHuman,
        ]);

        return HealthCheck::where([
            'resource_slug' => $target->resource->slug,
            'target_name' => $target->name,
        ])
            ->orderBy('created_at', 'desc')
            ->take(config('health.database.max_records'))
            ->get();
    }
}
