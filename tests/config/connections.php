<?php

use schibsted\payment\lib\Connections;

Connections::config('shared', [
    'host' => 'http://httpbin.org',
]);

// Connections::config('curl with proxy', [
//     'host' => 'http://httpbin.org',
//     'proxy' => [
//         'host' => 'localhost',
//         'port' => '8888',
//         'user' => 'ting',
//         'pass' => 'tang'
//     ]
// ]);
