<?php

namespace webnick\framework\core;

use webnick\helpers\SecurityHelper;

/**
 * Class Bootstrap класс для первоначальных операций фреймворка, перед запуском приложения
 *
 * @package webnick\framework\core
 */
class Bootstrap
{
    /**
     * Основной метод запуска
     *
     * @throws \Exception
     */
    public static function run(): void
    {
        if (APPTYPE == 'web')
            self::runWebBootstrap();
        elseif (APPTYPE == 'console')
            self::runConsoleBootstrap();

        self::runCommonBootstrap();
    }

    /**
     * Запустить bootstrap для web-приложения
     *
     * @throws \Exception
     */
    protected static function runWebBootstrap(): void
    {
        SecurityHelper::handleGlobalArrays();
    }

    /**
     * Запустить bootstrap для консоли
     */
    protected static function runConsoleBootstrap(): void
    {

    }


    /**
     * Запустить bootstrap общий и для web и для консоли
     */
    protected static function runCommonBootstrap(): void
    {

    }
}