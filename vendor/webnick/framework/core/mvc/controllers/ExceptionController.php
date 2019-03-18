<?php

namespace webnick\framework\core\mvc\controllers;

/**
 * Class ExceptionController базовый класс обработки исключений внутри контроллера ошибок
 *
 * @package webnick\framework\core\mvc\controllers
 */
class ExceptionController extends WebController
{
    /* @var $exception \Exception */
    protected $exception;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Установить объект исключения
     *
     * @param \Throwable $exception
     */
    public function setException(\Throwable $exception): void
    {
        $this->exception = $exception;
    }
}