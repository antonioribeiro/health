<?php

namespace PragmaRX\Health\Data\Models;

use Illuminate\Database\Eloquent\Model;

class HealthCheck extends Model
{
    protected $table = 'health_checks';

    protected $fillable = [
        'resource_name',
        'resource_slug',
        'target_name',
        'target_display',
        'healthy',
        'error_message',
        'runtime',
        'value',
        'value_human',
        'created_at',
    ];

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = null;
}
