<?php

namespace webnick\framework\core\mvc;

use webnick\framework\core\mvc\controllers\WebController;

/**
 * Class View класс вида
 *
 * @package webnick\framework\core\mvc
 */
class View
{
    protected $params = [];

    protected $controller;

    //директория с файлами вида
    protected $dir = null;

    public function __construct(WebController $controller)
    {
        $this->controller = $controller;

        $class = new \ReflectionClass($this->controller);

        $this->dir = realpath(dirname($class->getFileName()) . '/../views/' . strtolower(substr($short = $class->getShortName(), 0, strrpos($short, MVC::getControllerPostfix()))));
    }

    /**
     * Вернуть отрендериный файл вида
     *
     * @param string $file
     * @param array|null $params
     * @return string
     */
    public function render(string $file, array $params = null): string
    {
        $this->params = &$params;

        if (is_file($file))
            return $this->getFileContent($file);

        $file = $this->dir . '/' . $file . '.php';

        return $this->getFileContent($file);
    }

    /**
     * Получить контент файла
     *
     * @param string $file
     * @return string
     */
    protected function getFileContent(string $file): string
    {
        ob_start();

        require $file;

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }
}