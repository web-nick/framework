<?php

namespace webnick\framework\core;

use webnick\framework\helpers\PathHelper;

/**
 * Class Config класс для работы с конфигом
 *
 * @package framework\core
 */
class Config
{
    protected static $config = null;

    private final function __construct(){}

    /**
     * Получить конфиг
     *
     * @param string|null $section
     * @return mixed
     */
    public static function getConfig(string $section = null)
    {
        if (!self::$config)
            self::$config = require PathHelper::getPath('app') . '/configs/main.php';

        if ($section)
            return isset(self::$config[$section]) ? self::$config[$section] : self::$config['app'][$section];
        else
            return self::$config;
    }
}