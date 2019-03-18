<?php

namespace bootstrap;

use webnick\framework\core\mvc\controllers\Controller;
use webnick\framework\core\mvc\controllers\WebController;
use webnick\framework\core\mvc\controllers\ConsoleController;

/**
 * Class AppBootstrap класс для первоначальных операций приложения, перед его запуском
 *
 * @package bootstrap
 */
abstract class AppBootstrap
{
    /**
     * Запустить bootstrap приложения для web
     *
     * @param WebController $controller
     */
    public static function runWebBootstrap(WebController $controller): void
    {

    }

    /**
     * Запустить bootstrap приложения для консоли
     *
     * @param ConsoleController $controller
     */
    public static function runConsoleBootstrap(ConsoleController $controller): void
    {

    }

    /**
     * Запустить общий bootstrap приложения
     *
     * @param Controller $controller
     */
    public static function runCommonBootstrap(Controller $controller): void
    {
        
    }


    /**
     * Bootstrap конкретного web-модуля
     *
     * @param WebController $controller
     */
    abstract public static function runModuleBootstrap(WebController $controller): void;
}