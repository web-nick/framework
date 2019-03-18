<?php

namespace webnick\framework\core;

use webnick\framework\core\errors\ErrorsHandler;
use webnick\framework\core\mvc\MVC;
use webnick\framework\core\mvc\controllers\Controller;
use webnick\framework\core\mvc\controllers\WebController;
use webnick\framework\core\mvc\controllers\ConsoleController;
use webnick\framework\helpers\PathHelper;
use bootstrap\AppBootstrap;

/**
 * Class App Главный класс фреймворка
 *
 * @package framework\core
 */
class App
{
    /**
     * Запустить приложение
     *
     * @throws \Exception
     */
    public static function run(): void
    {
        define('APPTYPE', isset($_SERVER['argc']) ? 'console' : 'web');

        self::registerAutoload();

        ErrorsHandler::setHandlers();

        $controller = MVC::createController();

        self::runBootstrapers($controller);

        MVC::run($controller);
    }

    /**
     * Запустить все бутсраперы
     *
     * @param Controller $controller
     * @throws \Exception
     */
    protected static function runBootstrapers(Controller $controller): void
    {
        Bootstrap::run();

        AppBootstrap::runCommonBootstrap($controller);

        if ($controller instanceof WebController) {
            AppBootstrap::runWebBootstrap($controller);

            $moduleBootstraper = "\modules\\" . Routing::getRoute()['module'] . "\\bootstrap\\ModuleBootstrap";

            /* @var $moduleBootstraper AppBootstrap */
            if (file_exists(PathHelper::getPath('app') . str_replace('\\', '/', $moduleBootstraper) . '.php'))
                $moduleBootstraper::runModuleBootstrap($controller);

        } elseif ($controller instanceof ConsoleController)
            AppBootstrap::runConsoleBootstrap($controller);
    }

    /**
     * Autoloader для приложения
     *
     * @return bool
     */
    protected static function registerAutoload(): bool
    {
        return spl_autoload_register(function ($class) {
            $class = str_replace('\\', '/', $class) . '.php';

            $files = [
                PathHelper::getPath('app') . '/' . $class,
            ];

            foreach ($files as $file)
                (file_exists($file) and is_file($file)) and require_once $file;
        });
    }

    /**
     * Определить есть ли метка, о том запущено ли приложение на production-е
     *
     * @return bool
     */
    public static function isProduction(): bool
    {
        return ($env = getenv('APP_ENV') and $env == 'prod') ? true : false;
    }
}