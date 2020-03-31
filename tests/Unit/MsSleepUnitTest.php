<?php

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use function CasperEngl\CronMs\m_sleep;

class MsSleepUnitTest extends TestCase
{
    /**
     * @test
     */
    public function one_hundred_twenty_three_milliseconds()
    {
        $now = Carbon::now();
        
        m_sleep(123);

        $after = Carbon::now();

        $this->assertEquals($now->add(123, 'milliseconds')->timestamp, $after->timestamp);
    }

    /**
     * @test
     */
    public function two_thousand_milliseconds()
    {
        $now = Carbon::now();
        
        m_sleep(2000);

        $after = Carbon::now();

        $this->assertEquals($now->add(2, 'seconds')->timestamp, $after->timestamp);
    }
}
