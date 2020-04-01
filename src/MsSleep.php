<?php

use Carbon\Carbon;

if (!function_exists('m_sleep')) {
    /**
     * Returns true after sleep, false if the time limit has been reached
     * 
     * @param int|float|Carbon $time
     * @param Carbon $time_limit
     * @return bool
     */
    function m_sleep($time, Carbon $time_limit = null): bool {
        if ($time_limit && $time_limit->isPast()) {
            return false;
        }

        if ($time instanceof Carbon) {
            usleep($time->diffInMilliseconds(Carbon::now()) * 1000);
        } else {
            usleep($time * 1000);
        }

        return true;
    }
}
