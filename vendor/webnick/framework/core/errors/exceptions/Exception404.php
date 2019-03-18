<?php

namespace webnick\framework\core\errors\exceptions;

/**
 * Class Exception404 класс для обработки 404-х ошибок
 *
 * @package webnick\framework\core\errors\exceptions
 */
class Exception404 extends \Exception
{
    public function __construct(string $message = 'Страница не найдена', int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}