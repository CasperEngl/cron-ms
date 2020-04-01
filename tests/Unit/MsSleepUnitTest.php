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
        
        m_sleep(100);

        $after = Carbon::now();

        $this->assertTrue($now->add(110, 'milliseconds')->gte($after));
    }

    /**
     * @test
     */
    public function two_thousand_milliseconds()
    {
        $now = Carbon::now();
        
        m_sleep(2000);

        $after = Carbon::now();

        $this->assertTrue($now->add(2010, 'milliseconds')->gte($after));
    }

    /**
     * @test
     */
    public function with_carbon_instance_instead_of_ms()
    {
        $now = Carbon::now();
        
        m_sleep(Carbon::now()->add(100, 'milliseconds'));

        $after = Carbon::now();

        $this->assertTrue($now->add(110, 'milliseconds')->gte($after));
    }

    /**
     * @test
     */
    public function within_time_limit()
    {
        $this->assertTrue(
            m_sleep(
                100,
                Carbon::now()->add(200, 'ms')
            )
        );
    }

    /**
     * @test
     */
    public function exceeded_time_limit()
    {
        $this->assertFalse(
            m_sleep(
                100,
                Carbon::now()->sub(200, 'ms')
            )
        );
    }
}
