<?php

return [
    'secret' => env('JWT_SECRET'),
    'ttl'    => (int) env('JWT_TTL', 60 * 24 * 7), // minutes, default 7 days
    'algo'   => 'HS256',
];
