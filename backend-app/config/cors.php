<?php

return [

    'paths' => ['*'],
       'allowed_methods' => ['GET', 'POST', 'PUT', 'OPTIONS'],
       'allowed_origins' => ['http://localhost:5173'],
       'allowed_origins_patterns' => [],
        'allowed_headers' => ['*', 'Authorization'],
       'exposed_headers' => [],
       'max_age' => 0,
       'supports_credentials' => true,

];
