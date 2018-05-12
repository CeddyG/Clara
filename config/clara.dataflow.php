<?php

return [
    
    'path' => storage_path('dataflow/exports'),
    
    'route' => [
        
        'web' => [
            'prefix'        => 'admin',
            'middleware'    => ['web', 'access']
        ],
        'api' => [
            'prefix'        => '',
            'middleware'    => 'api'
        ]
    ],
    
    
];

