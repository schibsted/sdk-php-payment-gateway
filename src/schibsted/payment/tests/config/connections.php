<?php

use schibsted\payment\lib\Connections;

Connections::config('shared', [
    'host' => 'http://httpbin.org',
]);

// Connections::config('curl with proxy', [
    // 'proxy' => [
        // 'host' => '',
        // 'port' => '',
        // 'user' => '',
        // 'pass' => ''
    // ]
// ]);
