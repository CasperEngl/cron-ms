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
        $this->log('Running a new cron test... Expect wait time of 60 seconds.');
    }
    /**
     * @test
     */
    public function long_running_process_exits_before_sixty_seconds()
    {
        $start = Carbon::now();

        $cron = CronMs::fromMs(5000, function ($i) {
            for ($i = 0; $i < 10; $i++) {
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, "http://www.example.com/");
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                curl_exec($ch);
                curl_close($ch);
            }
        });

        $this->log((string) $start->diffInSeconds(Carbon::now()));

        $this->assertTrue($start->diffInSeconds(Carbon::now()) < 60);
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

            $this->assertEquals((5000 * $i) / 1000, round($now->floatDiffInSeconds($start), 1));
        });

        $this->assertEquals(60, $start->diffInSeconds(Carbon::now()));
    }
    
    /**
     * @test
     */
    public function five_and_a_half_thousand_milliseconds()
    {
        $start = Carbon::now();

        $cron = CronMs::fromMs(5500, function ($i) use ($start) {
            $now = Carbon::now();

            $this->assertEquals((5500 * $i) / 1000, round($now->floatDiffInSeconds($start), 1));
        });

        // Process must exit before 60 seconds, therefore,
        // the last run is completed after 55 seconds in this case
        $this->assertEquals(55, $start->diffInSeconds(Carbon::now()));
    }
    
    /**
     * @test
     */
    public function five_seconds()
    {
        $start = Carbon::now();

        $cron = CronMs::fromSeconds(5, function ($i) use ($start) {
            $now = Carbon::now();

            $this->assertEquals((5000 * $i) / 1000, round($now->floatDiffInSeconds($start), 1));
        });

        $this->assertEquals(60, $start->diffInSeconds(Carbon::now()));
    }
    
    /**
     * @test
     */
    public function five_and_a_half_second()
    {
        $start = Carbon::now();

        $cron = CronMs::fromSeconds(5.5, function ($i) use ($start) {
            $now = Carbon::now();

            $this->assertEquals((5500 * $i) / 1000, round($now->floatDiffInSeconds($start), 1));
        });

        // Process must exit before 60 seconds, therefore,
        // the last run is completed after 55 seconds in this case
        $this->assertEquals(55, $start->diffInSeconds(Carbon::now()));
    }
}
