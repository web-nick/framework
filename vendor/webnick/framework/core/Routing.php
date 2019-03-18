<?php

namespace webnick\framework\core;

use webnick\framework\helpers\PathHelper;
use webnick\framework\core\mvc\MVC;

/**
 * Class Routing класс создания роутинга для модуля, контроллера, действия
 *
 * @package framework\core
 */
class Routing
{
    protected static $route = null;

    /**
     * Отпарсить и получить компоненты роутинга
     *
     * @return array
     */
    public static function getRoute(): array
    {
        if (self::$route)
            return self::$route;

        $method = 'get' . APPTYPE . 'Route';

        return self::$method();
    }

    /**
     * Получить web-роутинг
     *
     * @return array
     */
    protected static function getWebRoute(): array
    {
        $url_route = array_filter(explode('/', parse_url($_SERVER['REQUEST_URI'])['path']));

        self::$route = [
            'module' => isset($url_route[1]) ? strtolower($url_route[1]) : 'main',
            'controller' => isset($url_route[2]) ? ucfirst(strtolower($url_route[2])) : 'Main',
            'action' => isset($url_route[3]) ? $url_route[3] : 'default'
        ];

        self::$route = self::getSubControllers($url_route, self::$route);

        //Обработка дефиса в роутинге
        foreach (self::$route as &$value)
            $value = self::handleHyphens($value);

        return self::$route;
    }

    /**
     * Получить консольный роутинг
     *
     * @return array
     */
    protected static function getConsoleRoute(): array
    {
        self::$route = [
            'controller' => 'Main',
            'action' => 'default'
        ];

        if (empty($action = getopt('a:', ['action::'])))
            return self::$route;

        $route = array_filter(explode('/', array_pop($action)));

        self::$route = [
            'controller' => isset($route[0]) ? ucfirst(strtolower($route[0])) : self::$route['controller'],
            'action' => isset($route[1]) ? $route[1] : self::$route['action']
        ];

        return self::$route;
    }

    /**
     * Получить вложенные субконтроллеры
     *
     * @param array $url_route
     * @param array $roure
     * @return array
     */
    protected static function getSubControllers(array $url_route, array $roure): array
    {
        //уровень вложенности не может быть меньше 2х
        if (count($url_route) < 2)
            return $roure;

        //первым в пути идет модуль, - удаляем его
        array_shift($url_route);

        for ($dir = PathHelper::getPath('app') . '/modules/' . $roure['module'] . '/controllers/' . array_shift($url_route) . '/', $sub_controller = '';
             is_dir($dir);
             $dir .= array_shift($url_route) . '/') {

            $sub_controller .= pathinfo($dir)['basename'] . '\\';

            $controller = self::handleHyphens(ucfirst(current($url_route)));

            if (is_file($dir . $controller . MVC::getControllerPostfix() . '.php')) {
                $roure['controller'] = $sub_controller . $controller;
                $roure['action'] = ($action = next($url_route)) ? $action : 'default';
            } elseif (is_file($dir . 'Default' . MVC::getControllerPostfix() . '.php')) {
                $roure['controller'] = $sub_controller . 'Default';
                $roure['action'] = ($action = current($url_route)) ? $action : 'default';
            }
        }

        return $roure;
    }

    /**
     * Обработать дефисы в роутинге
     *
     * @param string $str
     * @return string
     */
    protected static function handleHyphens(string $str): string
    {
        return preg_replace_callback('~-(\w)~', function ($matches) {
            return strtoupper($matches[1]);
        }, $str);
    }

    /**
     * Проверяет, что переданный в роутинге модуль и контроллер присутствуют в виде файла
     *
     * @param array $routing
     * @return bool
     */
    public static function isWebRouteFileExist(array $routing): bool
    {
        $file = str_replace(
            '\\',
            '/',
            PathHelper::getPath('app') . '/modules/' . $routing['module'] . '/controllers/' . $routing['controller'] . MVC::getControllerPostfix() . '.php'
        );

        if (!is_file($file))
            return false;

        return true;
    }
}