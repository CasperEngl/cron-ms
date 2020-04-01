<?php

use Carbon\Carbon;
use CasperEngl\CronMs\CronMs;
use PHPUnit\Framework\TestCase;
use CasperEngl\CronMs\UnsafeException;

class CronMsUnitTest extends TestCase
{
    public function setUp(): void
    {
        Carbon::setTestNow();
    }

    /**
     * @test
     */
    public function is_cron_ms_instance()
    {
        $cron = CronMs::fromMs(5000, null, function () {}, false);

        $this->assertInstanceOf(CronMs::class, $cron);
    }

    /**
     * @test
     */
    public function five_thousand_milliseconds()
    {
        $cron = CronMs::fromMs(5000, null, function () {}, false);

        $this->assertEquals(5000, $cron->ms);
    }
    
    /**
     * @test
     */
    public function five_seconds()
    {
        $cron = CronMs::fromSeconds(5, null, function () {}, false);

        $this->assertEquals(5000, $cron->ms);
    }

    /**
     * @test
     */
    public function has_start_timestamp()
    {
        $cron = CronMs::fromMs(5000, null, function () {}, false);

        $this->assertEquals(Carbon::now()->timestamp, $cron->start_timestamp);
    }

    /**
     * @test
     */
    public function has_start_carbon_instance()
    {
        $cron = CronMs::fromMs(5000, null, function () {}, false);

        $this->assertEquals(Carbon::class, get_class($cron->start));
    }
    /**
     * @test
     */
    public function from_ms_unsafe_allowed()
    {
        $cron = CronMs::fromMs(300, null, function () {}, false, true);

        $this->addToAssertionCount(1);
    }

    /**
     * @test
     */
    public function from_ms_unsafe_disallowed()
    {
        $this->expectException(UnsafeException::class);
        $this->expectExceptionMessage('$ms is less than 500ms. This may not be the desired behavior. Make sure to turn on the $unsafe flag to proceed.');

        $cron = CronMs::fromMs(300, null, function () {}, false);
    }

    /**
     * @test
     */
    public function from_seconds_unsafe_allowed()
    {
        $cron = CronMs::fromSeconds(0.3, null, function () {}, false, true);

        $this->addToAssertionCount(1);
    }

    /**
     * @test
     */
    public function from_seconds_unsafe_disallowed()
    {
        $this->expectException(UnsafeException::class);
        $this->expectExceptionMessage('$ms is less than 500ms. This may not be the desired behavior. Make sure to turn on the $unsafe flag to proceed.');

        $cron = CronMs::fromSeconds(0.3, null, function () {}, false);
    }

}
