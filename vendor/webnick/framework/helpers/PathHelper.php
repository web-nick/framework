<?php

namespace webnick\framework\helpers;

/**
 * Class PathHelper helper по работе с путями фреймворка
 *
 * @package application\helpers
 */
class PathHelper
{
    protected static $paths = [
        'pub' => ROOT_DIR . DIRECTORY_SEPARATOR . 'public',
        'app' => ROOT_DIR . DIRECTORY_SEPARATOR . 'application',
        'tmp' => ROOT_DIR . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'tmp',
        'log' => ROOT_DIR . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs',
    ];

    /**
     * Вернуть путь по псевдониму
     *
     * @param string $alias
     * @return string
     */
    public static function getPath(string $alias): string
    {
        return self::$paths[$alias];
    }
}