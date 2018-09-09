<?php

namespace PragmaRX\Health\Support;

use Illuminate\Support\Collection;
use PragmaRX\Health\Support\Traits\ToArray;
use PragmaRX\Health\Events\RaiseHealthIssue;
use PragmaRX\Health\Support\Traits\MagicData;

class Resource
{
    use MagicData, ToArray;

    /**
     * @var Collection
     */
    public $data;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $slug;

    /**
     * @var string
     */
    public $abbreviation;

    /**
     * @var bool
     */
    public $isGlobal;

    /**
     * @var string
     */
    public $errorMessage;

    /**
     * @var int
     */
    public $columnSize;

    /**
     * @var bool
     */
    public $notify;

    /**
     * @var Collection
     */
    public $targets;

    /**
     * @var ResourceChecker
     */
    public $checker;

    /**
     * @var Collection
     */
    public $resources;

    /**
     * @var bool
     */
    private $notified;

    /**
     * @var string
     */
    private $currentAction;

    /**
     * Resource factory.
     *
     * @param Collection $data
     * @return resource
     */
    public static function factory(Collection $data)
    {
        $instance = new static();

        $instance->data = $data;

        $instance->name = $instance->data['name'];

        $instance->slug = str_slug($instance->data['name']);

        $instance->abbreviation = $instance->data['abbreviation'];

        $instance->targets = $instance->instantiateTargets(
            $instance->data['targets'] ?? collect()
        );

        $instance->notify =
            $instance->data['notify'] ?? config('health.notifications.enabled');

        $instance->columnSize =
            $instance->data['column_size'] ??
            config('health.columns.default_size');

        $instance->errorMessage =
            $instance->data['error_message'] ?? config('health.errors.message');

        $instance->isGlobal = $instance->data['is_global'] ?? false;

        $instance->checker = $instance->instantiateChecker(
            $instance->data['checker']
        );

        return $instance;
    }

    /**
     * Instantiate all checkers for a resource.
     *
     * @param Collection $targets
     * @return Collection|\IlluminateAgnostic\Arr\Support\Collection|\IlluminateAgnostic\Collection\Support\Collection|\IlluminateAgnostic\Str\Support\Collection|mixed|\Tightenco\Collect\Support\Collection|\Vanilla\Support\Collection
     */
    public function instantiateTargets(Collection $targets)
    {
        if ($targets->isEmpty()) {
            return collect([Target::factory($this, $targets)]);
        }

        $current = collect();

        $targets = $targets
            ->map(function (Collection $targetList) {
                return $targetList->map(function ($target) {
                    return Target::factory($this, $target);
                });
            })
            ->reduce(function ($current, $targetList) {
                foreach ($targetList as $target) {
                    $current[] = $target;
                }

                return $current;
            }, $current);

        return $targets;
    }

    /**
     * Instantiate one checker.
     *
     * @param string $checker
     * @return object
     */
    public function instantiateChecker(string $checker)
    {
        return instantiate($checker);
    }

    /**
     * Check all targets for a resource.
     *
     * @return resource
     */
    public function check()
    {
        $this->targets->each(function (Target $target) {
            $target->check($target);
        });

        $this->notify();

        return $this;
    }

    /**
     * Check global resources.
     *
     * @param $resources
     * @return resource
     */
    public function checkGlobal($resources)
    {
        return $this->setResources($resources)->check();
    }

    /**
     * Check if is healthy.
     *
     * @return mixed
     */
    public function isHealthy()
    {
        return $this->targets->reduce(function ($carry, $target) {
            return $carry && $target->result->healthy;
        }, true);
    }

    /**
     * Notify about health problems.
     */
    protected function notify()
    {
        if ($this->canNotify()) {
            $this->sendNotifications();
        }
    }

    /**
     * Send notifications.
     *
     * @return static
     */
    protected function sendNotifications()
    {
        return collect(config('health.notifications.channels'))->each(function (
            $channel
        ) {
            try {
                event(new RaiseHealthIssue($this, $channel));
            } catch (\Exception $exception) {
                // Notifications are broken, just report it
                report($exception);
            }
        });
    }

    /**
     * Can we notify about errors on this resource?
     *
     * @return bool
     */
    protected function canNotify()
    {
        return
            ! $this->notified &&
            $this->notificationsAreEnabled() &&
            ! $this->isHealthy();
    }

    /**
     * Is notification enabled for this resource?
     *
     * @return bool
     */
    protected function notificationsAreEnabled()
    {
        return
            $this->notify &&
            config('health.notifications.enabled') &&
            config('health.notifications.notify_on.'.$this->currentAction);
    }

    /**
     * Resources setter.
     *
     * @param $resources
     * @return resource
     */
    protected function setResources($resources)
    {
        $this->resources = $resources;

        return $this;
    }

    /**
     * Object to json.
     *
     * @return false|string
     */
    public function __toString()
    {
        return json_encode($this->__toArray($this, 6));
    }
}
