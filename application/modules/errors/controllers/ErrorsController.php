<?php

namespace modules\errors\controllers;

use webnick\framework\core\mvc\controllers\ExceptionController;
use webnick\framework\core\App;
use webnick\framework\helpers\PathHelper;
use webnick\framework\core\Routing;

/**
 * Class ErrorsController класс для обработки ошибок
 *
 * @package modules\errors\controllers
 */
class ErrorsController extends ExceptionController
{
    /**
     * Обработка 404-х ошибок
     */
    public function Exception404(): string
    {
        header('HTTP/1.1 404 Not Found');

        return $this->view->render('exception404', [
            'message' => $this->exception->getMessage()
        ]);
    }

    /**
     * Обработка всех остальных ошибок
     *
     * @throws \Exception
     */
    public function Exceptions(): string
    {
        if (!App::isProduction())
            throw new \Exception($this->exception->getMessage(), (int)$this->exception->getCode(), $this->exception);

        header('HTTP/1.1 503 Service Unavailable');

        self::addToLog($this->exception);

        return 'Произошла ошибка на сервере';
    }

    /**
     * Добавить запись об ошибках в лог
     *
     * @param \Throwable $exception
     * @return int
     */
    protected static function addToLog(\Throwable $exception): int
    {
        $file = PathHelper::getPath('log') . '/errors.log';

        $message = "Error: '{$exception->getMessage()}', in file: '{$exception->getFile()}', in line: {$exception->getLine()} \r\n";

        return file_put_contents($file, $message, FILE_APPEND);
    }

}