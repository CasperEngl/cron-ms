# Cron Milliseconds

![https://scrutinizer-ci.com/g/CasperEngl/cron-ms?b=master](https://scrutinizer-ci.com/g/CasperEngl/cron-ms/badges/quality-score.png?b=master)
![https://scrutinizer-ci.com/g/CasperEngl/cron-ms?b=master](https://scrutinizer-ci.com/g/CasperEngl/cron-ms/badges/build.png?b=master)

This library makes it easy to run a piece of code, every X milliseconds or seconds.

```php
// Initialization and running

// From milliseconds
$cron = CronMs::fromMs(5000, function ($i /* each iteration count is returned here, start at 0 */) {
    // ... your code
});

// From seconds
$cron = CronMs::fromSeconds(5, function ($i /* each iteration count is returned here, start at 0 */) {
    // ... your code
});


// Initialization only

// You can also start the process yourself later.
// Just pass false as the third (3rd) parameter
$cron = CronMs::fromMs(5000, function ($i /* each iteration count is returned here, start at 0 */) {
  // ... your code
}, false /* disables automatic run */);

// Some other code

// Some time later
$cron->run();


// Properties

// You can access `$ms`, `$start_timestamp` and `$startTime` from the CronMs instance
$cron = CronMs::fromMs(5000, function ($i /* each iteration count is returned here, start at 0 */) {
  // ... your code
});

$cron->ms // 5000
$cron->start_timestamp // unix timestamp
$cron->start // Carbon\Carbon instance
```

# Testing

Run the `./vendor/bin/phpunit` (optionally with the `--testdox` parameter, for pretty output). Be aware, tests are longrunning. This is because each test runs the `u_sleep()` method internally.