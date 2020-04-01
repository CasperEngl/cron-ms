<?php

use Carbon\Carbon;
use CasperEngl\CronMs\CronMs;
use PHPUnit\Framework\TestCase;

class CronMsUnitTest extends TestCase
{
    /**
     * @test
     */
    public function is_cron_ms_instance()
    {
        $cron = CronMs::fromMs(5000, function () {}, false);

        $this->assertInstanceOf(CronMs::class, $cron);
    }

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

    /**
     * @test
     */
    public function has_start_timestamp()
    {
        Carbon::setTestNow();

        $cron = CronMs::fromMs(5000, function () {}, false);

        $this->assertEquals(Carbon::now()->timestamp, $cron->startTimeStamp);
    }

    /**
     * @test
     */
    public function has_start_carbon_instance()
    {
        Carbon::setTestNow();

        $cron = CronMs::fromMs(5000, function () {}, false);

        $this->assertEquals(Carbon::class, get_class($cron->start));
    }
}
