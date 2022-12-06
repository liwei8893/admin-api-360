<?php

declare(strict_types=1);

use Hyperf\Watcher\Driver\ScanFileDriver;

return [
    'driver' => ScanFileDriver::class,
    'bin' => 'php',
    'watch' => [
        'dir' => ['api', 'app', 'config', 'mine'],
        'file' => ['.env'],
        'scan_interval' => 2000,
    ],
];
