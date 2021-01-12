<?php

namespace PragmaRX\Health\Tests\PhpUnit\Service;

use PragmaRX\Health\Support\Timer;
use PragmaRX\Health\Tests\PhpUnit\TestCase;

class TimerTest extends TestCase
{
    public function testCanStartTimer()
    {
        Timer::start();

        sleep(1);

        $this->assertEquals(1, (int) Timer::stop());
    }
}
