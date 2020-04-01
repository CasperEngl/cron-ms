<?php

use Carbon\Carbon;

if (!function_exists('msleep')) {
    /**
     * @param int|float|Carbon $time
     * @param Carbon $time_limit
     * @return bool True after sleep, false if the time limit has been reached
     */
    function msleep($time, Carbon $time_limit = null): bool {
        // If negative time is received
        if (($time instanceof Carbon && $time->isPast()) || (!$time instanceof Carbon && $time < 0)) {
            trigger_error(
                'msleep(): Number of milliseconds must be greater than or equal to 0',
                E_USER_WARNING
            );
        }

        // Passed time limit
        if ($time_limit && $time_limit->isPast()) {
            // Return false when the time limit has been reached
            return false;
        }

        if ($time instanceof Carbon) {
            /**
             * Sleep for X milliseconds, depending on the milliseconds
             * time difference between the $time variable, and the
             * current time.
             * 
             * X * 1000 converts milliseconds to microseconds,
             * as is required by usleep().
             * 
             * @link https://www.php.net/manual/en/function.usleep.php
             */
            usleep($time->diffInMilliseconds(Carbon::now()) * 1000);
        } else {
            /**
             * Sleep for X milliseconds
             * 
             * X * 1000 converts milliseconds to microseconds,
             * as is required by usleep().
             * 
             * @link https://www.php.net/manual/en/function.usleep.php
             */
            usleep($time * 1000);
        }

        // Return true after the process has slept
        return true;
    }
}

if (!function_exists('m_sleep')) {
    /**
     * Alias for `msleep`
     * 
     * @see function msleep
     */
    function m_sleep() {
        return call_user_func_array('msleep', func_get_args());
    }
}

if (!function_exists('mSleep')) {
    /**
     * Alias for `msleep`
     * 
     * @see function msleep
     */
    function mSleep() {
        return call_user_func_array('msleep', func_get_args());
    }
}
