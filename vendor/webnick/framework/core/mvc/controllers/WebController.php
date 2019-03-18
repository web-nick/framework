<?php

namespace webnick\framework\core\mvc\controllers;

use webnick\framework\core\mvc\View;
use webnick\framework\core\mvc\Layout;

/**
 * Class Controller базовый класс web-контроллера
 *
 * @package framework\core\mvc\controllers
 */
class WebController extends Controller
{
    protected $view;

    protected $layout;

    public function __construct()
    {
        $this->view = new View($this);

        $this->layout = new Layout;
    }

    /**
     * Вернуть объект шаблона
     *
     * @return Layout
     */
    public function getLayout(): Layout
    {
        return $this->layout;
    }

    /**
     * Вернуть объект вида
     *
     * @return View
     */
    public function getView(): View
    {
        return $this->view;
    }
}