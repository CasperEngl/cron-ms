<?php

namespace CasperEngl\CronMs;

use Closure;
use Exception;
use Carbon\Carbon;
use function CasperEngl\CronMs\m_sleep;

class MillisecondsException extends Exception
{
    protected $message = '$ms is not set in constructor. Initialize with fromMs or fromSeconds';
}

class CronMs
{
    const MINUTE = 60000;

    protected Closure $fn;

    public int $ms;

    public Carbon $start;

    public int $startTimeStamp;

    public function __construct(int $ms, callable $fn)
    {
        $this->ms = $ms;

        $this->fn = Closure::fromCallable($fn);

        $this->start = Carbon::now();
        
        $this->startTimeStamp = $this->start->timestamp;
    }

    public static function fromMs(int $ms, callable $fn, $run_immediately = true): self
    {
        $cron = new self($ms, $fn);

        if ($run_immediately) {
            $cron->run();
        }

        return $cron;
    }

    public static function fromSeconds(float $seconds, callable $fn, $run_immediately = true): self
    {
        $cron = new self($seconds * 1000, $fn);

        if ($run_immediately) {
            $cron->run();
        }

        return $cron;
    }

    public function run(): self
    {
        set_time_limit(60);

        $division = self::MINUTE / $this->ms;

        for ($i = 0; $i < floor($division); $i++) {
            \call_user_func($this->fn, $i);

            if (! m_sleep(round(self::MINUTE / $division, 2), $this->start->add(60, 'seconds')->timestamp)) {
                break;
            }
        }

        return $this;
    }
}
