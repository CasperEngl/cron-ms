<?php

use Carbon\Carbon;
use CasperEngl\CronMs\CronMs;
use PHPUnit\Framework\TestCase;

class CronMsUnitTest extends TestCase
{
    /**
     * @test
     */
    public function five_thousand_milliseconds()
    {
        $cron = CronMs::fromMs(5000, function () {}, false);

        $this->assertEquals(5000, $cron->ms);
    }
    
    /**
     * @test
     */
    public function five_seconds()
    {
        $cron = CronMs::fromSeconds(5, function () {}, false);

        $this->assertEquals(5000, $cron->ms);
    }
}
