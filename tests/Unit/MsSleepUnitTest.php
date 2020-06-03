<?php

use Carbon\Carbon;
use CasperEngl\CronMs\CronMs;
use CasperEngl\CronMs\MillisecondsMustBePositiveException;
use CasperEngl\CronMs\TimeLimitExceededException;
use PHPUnit\Framework\TestCase;

/**
 * Some uncertainty may be present when dealing with milliseconds.
 * 
 * To account for this uncertainty, about 10 milliseconds should
 * be added to each test
 */

class MsSleepUnitTest extends TestCase
{
    public CronMs $cron;

    public function setUp(): void
    {
        $this->cron = CronMs::fromMs(1000, function () {}, null, false);
    }

    /**
     * @test
     */
    public function one_hundred_twenty_three_milliseconds()
    {
        $now = Carbon::now();
        
        $this->cron->msleep(100);

        $after = Carbon::now();

        $this->assertTrue($now->addMilliseconds(110)->gte($after));
    }

    /**
     * @test
     */
    public function two_thousand_milliseconds()
    {
        $now = Carbon::now();
        
        $this->cron->msleep(2000);

        $after = Carbon::now();

        $this->assertTrue($now->addMilliseconds(2010)->gte($after));
    }

    /**
     * @test
     */
    public function with_carbon_instance_instead_of_ms()
    {
        $now = Carbon::now();
        
        $this->cron->msleep(Carbon::now()->addMilliseconds(100));

        $after = Carbon::now();

        $this->assertTrue($now->addMilliseconds(110)->gte($after));
    }

    /**
     * @test
     */
    public function within_time_limit()
    {
        $this->assertTrue(
            $this->cron->msleep(
                100,
                Carbon::now()->addMilliseconds(200)
            )
        );
    }

    /**
     * @test
     */
    public function exceeded_time_limit()
    {
        $this->expectException(TimeLimitExceededException::class);

        $this->cron->msleep(
            100,
            Carbon::now()->subMilliseconds(200)
        );
    }

    /**
     * @test
     */
    public function negative_time_value_warning_triggered()
    {
        $this->expectException(MillisecondsMustBePositiveException::class);
        $this->expectExceptionMessage('msleep(): Number of milliseconds must be greater than or equal to 0');

        $this->cron->msleep(-500);
    }
}
