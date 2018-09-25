<?php

namespace PragmaRX\Health\Support;

class Timer
{
    public static function start()
    {
        return class_exists('SebastianBergmann\Timer\Timer')
            ? \SebastianBergmann\Timer\Timer::start()
            : \PHP_Timer::start();
    }

    public static function stop()
    {
        return class_exists('SebastianBergmann\Timer\Timer')
            ? \SebastianBergmann\Timer\Timer::stop()
            : \PHP_Timer::stop();
    }
}
