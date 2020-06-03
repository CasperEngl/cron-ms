<?php

use Carbon\Carbon;
use CasperEngl\CronMs\CronMs;
use CasperEngl\CronMs\UnsafeException;
use PHPUnit\Framework\TestCase;

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
        $cron = CronMs::fromMs(5000, function () {}, null, false);

        $this->assertInstanceOf(CronMs::class, $cron);
    }

    /**
     * @test
     */
    public function five_thousand_milliseconds()
    {
        $cron = CronMs::fromMs(5000, function () {}, null, false);

        $this->assertEquals(5000, $cron->milliseconds);
    }
    
    /**
     * @test
     */
    public function five_seconds()
    {
        $cron = CronMs::fromSeconds(5, function () {}, 60000, false);

        $this->assertEquals(5000, $cron->milliseconds);
    }

    /**
     * @test
     */
    public function has_start_timestamp()
    {
        $cron = CronMs::fromMs(5000, function () {}, null, false);

        $this->assertEquals(Carbon::now()->valueOf(), $cron->start->valueOf());
    }

    /**
     * @test
     */
    public function has_start_carbon_instance()
    {
        $cron = CronMs::fromMs(5000, function () {}, null, false);

        $this->assertEquals(Carbon::class, get_class($cron->start));
    }
    /**
     * @test
     */
    public function from_ms_unsafe_allowed()
    {
        $cron = CronMs::fromMs(300, function () {}, null, false, true);

        $this->addToAssertionCount(1);
    }

    /**
     * @test
     */
    public function from_ms_unsafe_disallowed()
    {
        $this->expectException(UnsafeException::class);
        $this->expectExceptionMessage('$milliseconds is less than 500ms. This may not be the desired behavior. Make sure to turn on the $unsafe flag to proceed.');

        $cron = CronMs::fromMs(300, function () {}, 60000, false, false);
    }

    /**
     * @test
     */
    public function from_seconds_unsafe_allowed()
    {
        $cron = CronMs::fromSeconds(0.3, function () {}, 60000, false, true);

        $this->addToAssertionCount(1);
    }

    /**
     * @test
     */
    public function from_seconds_unsafe_disallowed()
    {
        $this->expectException(UnsafeException::class);
        $this->expectExceptionMessage('$milliseconds is less than 500ms. This may not be the desired behavior. Make sure to turn on the $unsafe flag to proceed.');

        $cron = CronMs::fromSeconds(0.3, function () {}, 60000, false, false);
    }

}
