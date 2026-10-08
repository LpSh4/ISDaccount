<?php

return [
    'token' => env('TELEGRAM_TOKEN'),
    'timeout' => 60,
    'client_options' => [
        'timeout' => 60,
        'connect_timeout' => 10,
    ],
    'poller' => [
        'timeout' => 10,
    ],
];
