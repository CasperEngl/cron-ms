<?php

namespace CasperEngl\CronMs;

use Closure;
use Exception;
use Carbon\Carbon;

class CronMs
{
    const MINUTE = 60000;

    public int $ms;
    
    public float $time_limit;

    public bool $unsafe;
    
    protected Closure $fn;

    public Carbon $start;

    public int $start_timestamp;

    protected float $execution_time = 0;

    public function __construct(
        int $ms,
        $time_limit,
        $fn,
        bool $unsafe
    ) {
        /**
         * Swap $time_limit and $fn if $time_limit is non-numeric
         * 
         * This allows the second parameter to be $fn, thus removing
         * the need to define a $time_limit
         */
        if (! is_numeric($time_limit)) {
            [$fn, $time_limit] = [$time_limit, $fn];
        }

        $this->ms = $ms;

        $this->time_limit = $time_limit ?: 60000;

        $this->unsafe = $unsafe;

        $this->fn = Closure::fromCallable($fn);

        $this->start = Carbon::now();
        
        $this->start_timestamp = $this->start->timestamp;
    }

    /**
     * Returns an instance of CronMs
     * @param int $ms
     * @param float|callable $time_limit
     * @param callable|null $fn
     * @param bool $run_immediately
     * @param bool $unsafe
     * @return self
     */
    public static function fromMs(
        int $ms,
        $time_limit = null,
        $fn = null,
        $run_immediately = true,
        $unsafe = false
    ): self {
        $cron = new self($ms, $time_limit, $fn, $unsafe);

        $cron->checkUnsafe();

        if ($run_immediately) {
            $cron->run();
        }

        return $cron;
    }

    /**
     * Returns an instance of CronMs
     * @param int $ms
     * @param float|callable $time_limit
     * @param callable|null $fn
     * @param bool $run_immediately
     * @param bool $unsafe
     * @return self
     */
    public static function fromSeconds(
        float $seconds,
        $time_limit = null,
        $fn = null,
        $run_immediately = true,
        $unsafe = false
    ): self {
        $cron = new self($seconds * 1000, $time_limit, $fn, $unsafe);

        $cron->checkUnsafe();

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
            $time_start = microtime(true);

            call_user_func($this->fn, $i);

            $time_end = microtime(true);

            if (! m_sleep(
                self::MINUTE / $division,
                $this->getLimit()
            )) {
                break;
                throw new Exception('Execution exceeded limit.');
            }

            // When $start has been subtracted from $end, we're left
            // with the execution time in microseconds
            // https://www.php.net/manual/en/function.microtime.php
            // https://stackoverflow.com/a/17035868
            if ($this->execution_time) {
                // Compare to last execution time
                $this->execution_time = ($this->execution_time + ($time_end - $time_start) * 1000) / 2;
            } else {
                // Set first execution time
                $this->execution_time = ($time_end - $time_start) * 1000;
            }
        }

        return $this;
    }

    protected function checkUnsafe()
    {
        if (! $this->unsafe && $this->ms < 500) {
            throw new Exception('$ms is less than 500ms. This may not be the desired behavior. Make sure to turn on the $unsafe flag to proceed.');
        }
    }

    protected function getLimit()
    {
        return $this->start->copy()
            ->add($this->time_limit, 'ms')
            ->sub($this->execution_time, 'ms');
    }
}
