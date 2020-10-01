<?php

namespace PragmaRX\Health\Support;

use SebastianBergmann\Timer\Timer as SBTimer;

class Timer
{
    private static $sbTimer;

    public static function start()
    {
        return class_exists('SebastianBergmann\Timer\Timer')
            ? static::startSB()
            : \PHP_Timer::start();
    }

    public static function stop()
    {
        return class_exists('SebastianBergmann\Timer\Timer')
            ? static::stopSB()
            : \PHP_Timer::stop();
    }

    public static function startSB()
    {
        return static::isStatic()
            ? SBTimer::start()
            : static::getSBInstance()->start();
    }

    public static function stopSB()
    {
        return static::isStatic()
            ? SBTimer::stop()->asSeconds()
            : static::getSBInstance()->stop()->asSeconds();
    }

    public static function isStatic()
    {
        return static::getStaticMethodNames()->contains('start');
    }

    public static function getSBInstance()
    {
        if (! empty(static::$sbTimer)) {
            return static::$sbTimer;
        }

        return static::$sbTimer = new SBTimer();
    }

    public static function getStaticMethodNames()
    {
        return collect((new \ReflectionClass(SBTimer::class))->getMethods(\ReflectionMethod::IS_STATIC))->map(function ($method) {
            return $method->name;
        });
    }
}
