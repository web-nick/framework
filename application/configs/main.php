<?php

use webnick\framework\helpers\PathHelper;

require_once 'directives.php';

return [
    'db' => require 'local/db.php',

    'layout' => [
        'title' => [
            'name' => 'Имя сайта',
            'separator' => ' | ',
        ],
    ],

    'pagination' => [
        'limit' => 30,
        'getParam' => 'page',
    ],

    'user' => [
        'session' => [
            'params' => [
                'save_path' => '2;' . PathHelper::getPath('tmp') . '/sessions/',
                'cookie_lifetime' => 31536000,
            ],
            'disable_gc' => true,
        ]
    ],

    'app' => require 'app.php',
];