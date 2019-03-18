<?php

namespace webnick\framework\core\mvc;

use webnick\framework\core\Routing;
use webnick\framework\core\errors\exceptions\Exception404;
use webnick\framework\core\mvc\controllers\Controller;
use webnick\framework\core\mvc\controllers\WebController;
use webnick\framework\core\mvc\controllers\ConsoleController;

/**
 * Class MVC класс обработки MVC
 *
 * @package framework\core
 */
class MVC
{
    protected static $controllerPostfix = 'Controller';

    /**
     * Создать и запустить контроллер
     *
     * @param Controller $controller
     * @throws Exception404
     */
    public static function run(Controller $controller): void
    {
        if (method_exists($controller, 'init'))
            $controller->init();

        if ($controller->action)
            $action = $controller->action;
        else
            $action = $controller->action = Routing::getRoute()['action'] . 'Action';

        if (!method_exists($controller, $action))
            throw new Exception404();

        $action = (new \ReflectionClass($controller))->getMethod($action);

        foreach ($action->getParameters() as $param)
            $args[] = $_GET[$param->name] ?? ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : null);

        $content = $action->invokeArgs($controller, $args ?? []);

        if ($controller instanceof WebController)
            $controller->getLayout()->render($content);
    }

    /**
     * Создать контроллер
     *
     * @return Controller
     */
    public static function createController(): Controller
    {
        $routing = Routing::getRoute();

        $method = 'create' . APPTYPE . 'Controller';

        /* @var $controller Controller */
        $controller = self::$method($routing);

        return $controller;
    }

    /**
     * Создать объект web-контроллера
     *
     * @param array $routing
     * @return WebController
     * @throws Exception404
     */
    protected static function createWebController(array $routing): WebController
    {
        if (!Routing::isWebRouteFileExist($routing))
            throw new Exception404();

        $controller = '\modules\\' . $routing['module'] . '\controllers\\' . $routing['controller'] . self::$controllerPostfix;

        return new $controller();
    }

    /**
     * Создать объект консольного контроллера
     *
     * @param array $routing
     * @return ConsoleController
     */
    protected static function createConsoleController(array $routing): ConsoleController
    {
        $controller = '\commands\\' . $routing['controller'] . self::$controllerPostfix;

        return new $controller();
    }

    /**
     * Возвращяет постфикс файлов контроллера
     *
     * @return string
     */
    public static function getControllerPostfix(): string
    {
        return self::$controllerPostfix;
    }
}