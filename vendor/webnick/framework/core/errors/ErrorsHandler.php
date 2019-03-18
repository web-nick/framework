<?php

namespace webnick\framework\core\errors;

use webnick\framework\core\errors\exceptions\Exception404;
use webnick\framework\core\errors\exceptions\ErrorException;
use webnick\framework\core\mvc\MVC;
use modules\errors\controllers\ErrorsController;

/**
 * Class ErrorsHandler класс для установки обработчиков ошибок
 *
 * @package webnick\framework\core\errors
 */
class ErrorsHandler
{
    protected static $isHandlersSet = false;

    /**
     * Установить обработчики ошибок
     *
     * @return bool
     */
    public static function setHandlers(): bool
    {
        if (self::$isHandlersSet)
            return true;

        set_exception_handler(function (\Throwable $exception) {
            $controller = new ErrorsController();

            $controller->setException($exception);

            /* @var $exception \Exception */
            if ($exception instanceof Exception404)
                $controller->action = 'Exception404';
            else
                $controller->action = 'Exceptions';

            MVC::run($controller);
        });

        set_error_handler(function (int $level, string $message, string $file, int $line): void {
            throw new ErrorException($level, $message, $file, $line);
        }, E_ALL);

        return self::$isHandlersSet = true;
    }
}