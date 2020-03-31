<?php

namespace CasperEngl\CronMs;

use Carbon\Carbon;

if (! function_exists('m_sleep')) {
    /**
     * Returns true after sleep, false if the time limit has been reached
     * 
     * @param int $ms
     * @param int $time_limit
     * @return bool
     */
    function m_sleep(int $ms, int $time_limit = null): bool {
        if ($time_limit && $time_limit - time() < 0) {
            return false;
        }
        
        // usleep sleeps for x microseconds
        usleep($ms * 1000);

        return true;
    }
}
