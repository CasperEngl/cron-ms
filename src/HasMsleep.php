<?php

declare(strict_types=1);

namespace CasperEngl\CronMs;

use Carbon\Carbon;

trait HasMsleep
{
    /**
     * Sleep for X milliseconds, depending on the milliseconds
     * time difference between the $time variable, and the
     * current time.
     *
     * X * 1000 converts milliseconds to microseconds,
     * as is required by usleep().
     *
     * @link https://www.php.net/manual/en/function.usleep.php
     *
     * @param int|float|Carbon $time
     *
     * @return bool True after sleep, false if the time limit has been reached
     */
    public function msleep($time, ?Carbon $time_limit = null): bool
    {
        $this->negativeMsleepCheck($time);

        $this->passedTimeLimitMsleepCheck($time_limit);

        if ($time instanceof Carbon) {
            usleep((int) $time->diffInMilliseconds(Carbon::now()) * 1000);
        } else {
            usleep((int) $time * 1000);
        }

        // Return true after the process has slept
        return true;
    }

    /**
     * @param int|float|Carbon $time
     */
    private function negativeMsleepCheck($time): void
    {
        // If negative time is received
        if (
            ($time instanceof Carbon && $time->isPast()) ||
            (! $time instanceof Carbon && $time < 0)
        ) {
            throw new MillisecondsMustBePositiveException('msleep(): Number of milliseconds must be greater than or equal to 0');
        }
    }

    private function passedTimeLimitMsleepCheck(?Carbon $time_limit = null): void
    {
        if ($time_limit && $time_limit->isPast()) {
            throw new TimeLimitExceededException();
        }
    }
}
