<?php

define('ROOT_DIR', realpath(__DIR__ . '/../../../'));

spl_autoload_register(function ($class) {

    $class = str_replace('\\', '/', $class) . '.php';

    $files = [
        ROOT_DIR . '/' . $class,
        ROOT_DIR . '/vendor/' . $class,
    ];

    foreach ($files as $file)
        (file_exists($file) and is_file($file)) and require_once $file;
});