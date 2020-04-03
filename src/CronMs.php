<?php

declare(strict_types=1);

namespace CasperEngl\CronMs;

use Carbon\Carbon;
use Closure;

final class CronMs
{
    use HasMsleep;

    private const MINUTE = 60000;

    private int $milliseconds;

    private ?int $time_limit_ms;

    private bool $unsafe;

    private Closure $func;

    private Carbon $start;

    private float $execution_time = 0;

    public function __construct(int $milliseconds, callable $func, ?int $time_limit_ms, bool $unsafe)
    {
        $this->milliseconds = $milliseconds;

        $this->time_limit = $time_limit_ms ?? 60000;

        $this->unsafe = $unsafe;

        $this->func = Closure::fromCallable($func);

        $this->start = Carbon::now();
    }

    /**
     * @return int|float|bool|Closure|Carbon
     */
    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    /**
     * @return self Instance of CronMs
     */
    public static function fromMs(
        int $milliseconds,
        callable $func,
        ?int $time_limit_ms = null,
        bool $run_immediately = true,
        bool $unsafe = false
    ): self {
        $cron = new self($milliseconds, $func, $time_limit_ms, $unsafe);

        $cron->checkUnsafe();

        if ($run_immediately) {
            $cron->run();
        }

        return $cron;
    }

    /**
     * @return self Instance of CronMs
     */
    public static function fromSeconds(
        float $seconds,
        callable $func,
        ?int $time_limit_ms = null,
        bool $run_immediately = true,
        bool $unsafe = false
    ): self {
        $cron = new self((int) $seconds * 1000, $func, $time_limit_ms, $unsafe);

        $cron->checkUnsafe();

        if ($run_immediately) {
            $cron->run();
        }

        return $cron;
    }

    /**
     * Executes the function
     *
     * @return self Return the same instance
     */
    public function run(): self
    {
        if ($this->time_limit) {
            set_time_limit($this->time_limit * 1000);
        }

        $division = self::MINUTE / $this->milliseconds;
        $flooredDivision = floor($division);

        for ($i = 0; $i < $flooredDivision; $i++) {
            $this->callFunc($i);

            if (! $this->msleep(self::MINUTE / $division, $this->getLimit())) {
                break;
            }
        }

        return $this;
    }

    private function callFunc(): void
    {
        $time_start = microtime(true);

        call_user_func_array($this->func, func_get_args());

        $time_end = microtime(true);

        $this->updateExecutionTime($time_start, $time_end);
    }

    /**
     * When $start has been subtracted from $end, we're left
     * with the execution time in microseconds
     *
     * https://www.php.net/manual/en/function.microtime.php
     * https://stackoverflow.com/a/17035868
     *
     * Sets execution time to average of previous
     * execution time and new execution time
     *
     * Ensures we get as close to the time limit
     * as possible, so the program doesn't keep
     * running forever.
     */
    private function updateExecutionTime(
        float $time_start,
        float $time_end
    ): void {
        if ($this->execution_time) {
            $this->execution_time = (
                $this->execution_time +
                ($time_end - $time_start) *
                 1000
            ) / 2;
        } else {
            /**
             * Set first execution time
             */
            $this->execution_time = ($time_end - $time_start) * 1000;
        }
    }

    private function checkUnsafe(): void
    {
        if (! $this->unsafe && $this->milliseconds < 500) {
            throw new Unsafe('$milliseconds is less than 500ms. This may not be the desired behavior. Make sure to turn on the $unsafe flag to proceed.');
        }
    }

    private function getLimit(): Carbon
    {
        $limit = $this->start->copy();

        if ($this->time_limit) {
            $limit->add('ms', $this->time_limit);
        }

        if ($this->execution_time) {
            $limit->sub('ms', $this->execution_time);
        }

        return $limit;
    }
}
