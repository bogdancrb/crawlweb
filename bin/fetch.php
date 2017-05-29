#!/usr/bin/env php
<?php

if (PHP_SAPI !== 'cli') {
    echo 'Warning: Fetcher should be invoked via the CLI version of PHP, not the '.PHP_SAPI.' SAPI'.PHP_EOL;
}

require_once __DIR__ . '/../vendor/autoload.php';

use Fetcher\Console\Application;

if (function_exists('ini_set')) {
    @ini_set('display_errors', 1);
    $memoryInBytes = function ($value) {
        $unit = strtolower(substr($value, -1, 1));
        $value = (int) $value;
        switch($unit) {
            case 'g':
                $value *= 1024;
            // no break (cumulative multiplier)
            case 'm':
                $value *= 1024;
            // no break (cumulative multiplier)
            case 'k':
                $value *= 1024;
        }
        return $value;
    };
    $memoryLimit = trim(ini_get('memory_limit'));
    // Increase memory_limit if it is lower than 1GB
    if ($memoryLimit != -1 && $memoryInBytes($memoryLimit) < 1024 * 1024 * 1024) {
        @ini_set('memory_limit', '1G');
    }
    unset($memoryInBytes, $memoryLimit);
}

set_time_limit(0);

// run the command application
$application = new Application();
$application->run();
