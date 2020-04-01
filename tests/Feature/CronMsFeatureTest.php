<?php

use Carbon\Carbon;
use CasperEngl\CronMs\CronMs;
use PHPUnit\Framework\TestCase;

class CronMsFeatureTest extends TestCase
{
    public function log($content = null)
    {
        ob_end_clean();
        if (gettype($content) === 'string') {
            echo $content;
        } else {
            var_dump($content);
        }
        ob_start();
    }

    public function setUp(): void
    {
        $this->log("\n" . 'Running a new cron test... Expect wait time of 60 seconds.' . "\n\n");
    }

    /**
     * @test
     */
    public function push_to_array_every_five_thousand_milliseconds()
    {
        $arr = [];

        $cron = CronMs::fromMs(5000, function ($i) use (&$arr) {
            $this->assertCount($i, $arr);

            $arr[] = $i;
            
            $this->assertCount($i + 1, $arr);
        });

        $this->assertCount(12, $arr);
        $this->assertEquals([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], $arr);
    }

    /**
     * @test
     */
    public function five_thousand_milliseconds()
    {
        $start = Carbon::now();

        $cron = CronMs::fromMs(5000, function ($i) use ($start) {
            $now = Carbon::now();

            $this->assertTrue(
                $now
                    ->add(5000 * $i, 'ms')
                    ->between(
                        $now->copy()->sub(10, 'ms'),
                        $now->copy()->add(10, 'ms')
                    )
            );
        });

        $this->checkStartEnd($start);
    }
    
    /**
     * @test
     */
    public function five_and_a_half_thousand_milliseconds()
    {
        $start = Carbon::now();

        $cron = CronMs::fromMs(5500, function ($i) use ($start) {
            $now = Carbon::now();

            $this->assertTrue(
                $now
                    ->add(5500 * $i, 'ms')
                    ->between(
                        $now->copy()->sub(10, 'ms'),
                        $now->copy()->add(10, 'ms')
                    )
            );
        });

        $this->checkStartEnd($start);
    }
    
    /**
     * @test
     */
    public function five_seconds()
    {
        $start = Carbon::now();

        $cron = CronMs::fromSeconds(5, function ($i) use ($start) {
            $now = Carbon::now();

            $this->assertTrue(
                $now
                    ->add(5000 * $i, 'ms')
                    ->between(
                        $now->copy()->sub(10, 'ms'),
                        $now->copy()->add(10, 'ms')
                    )
            );
        });

        $this->checkStartEnd($start);
    }
    
    /**
     * @test
     */
    public function five_and_a_half_second()
    {
        $start = Carbon::now();

        $cron = CronMs::fromSeconds(5.5, function ($i) use ($start) {
            $now = Carbon::now();

            $this->assertTrue(
                $now
                    ->add(5500 * $i, 'ms')
                    ->between(
                        $now->copy()->sub(10, 'ms'),
                        $now->copy()->add(10, 'ms')
                    )
            );
        });

        $this->checkStartEnd($start);
    }

    /**
     * @test
     */
    public function long_running_process_exits_before_sixty_seconds()
    {
        $start = Carbon::now();

        $cron = CronMs::fromMs(5000, function ($i) {
            for ($i = 0; $i < 10; $i++) {
                sleep(0.725);
            }
        });

        $this->checkStartEnd($start);
    }

    protected function checkStartEnd(Carbon $start)
    {
        // More than 59.9 seconds
        $this->assertTrue($start->diffInMilliseconds(Carbon::now()) >= 59900);
        // Less than 60.1 seconds
        $this->assertTrue($start->diffInMilliseconds(Carbon::now()) <= 60100);
    }
}
