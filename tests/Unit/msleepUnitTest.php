<?php

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * Some uncertainty may be present when dealing with milliseconds.
 * 
 * To account for this uncertainty, about 10 milliseconds should
 * be added to each test
 */

class MsSleepUnitTest extends TestCase
{
    /**
     * @test
     */
    public function one_hundred_twenty_three_milliseconds()
    {
        $now = Carbon::now();
        
        msleep(100);

        $after = Carbon::now();

        $this->assertTrue($now->add(110, 'milliseconds')->gte($after));
    }

    /**
     * @test
     */
    public function two_thousand_milliseconds()
    {
        $now = Carbon::now();
        
        msleep(2000);

        $after = Carbon::now();

        $this->assertTrue($now->add(2010, 'milliseconds')->gte($after));
    }

    /**
     * @test
     */
    public function with_carbon_instance_instead_of_ms()
    {
        $now = Carbon::now();
        
        msleep(Carbon::now()->add(100, 'milliseconds'));

        $after = Carbon::now();

        $this->assertTrue($now->add(110, 'milliseconds')->gte($after));
    }

    /**
     * @test
     */
    public function within_time_limit()
    {
        $this->assertTrue(
            msleep(
                100,
                Carbon::now()->add('ms', 200)
            )
        );
    }

    /**
     * @test
     */
    public function exceeded_time_limit()
    {
        $this->assertFalse(
            msleep(
                100,
                Carbon::now()->sub('ms', 200)
            )
        );
    }

    /**
     * @test
     */
    public function negative_time_value_warning_triggered()
    {
        $this->expectWarning();
        $this->expectWarningMessage('msleep(): Number of milliseconds must be greater than or equal to 0');

        msleep(-500);
    }
}
