<?php
return [
        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/html',
        ],

        'db' => [
            'driver' => 'mysql',
            'host' => 'db',
            'database' => 'myDb',
            'username' => 'user',
            'password' => 'test',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
        // Monolog settings
//        'logger' => [
//            'name' => 'slim-app',
//            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
////            'level' => \Monolog\Logger::DEBUG,
//        ],
];