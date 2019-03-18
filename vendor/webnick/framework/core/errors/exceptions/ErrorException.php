<?php

namespace webnick\framework\core\errors\exceptions;

/**
 * Class ErrorException класс для обработки PHP-ошибок
 *
 * @package webnick\framework\core\errors\exceptions
 */
class ErrorException extends \Error
{
    protected $level = false;

    public function __construct(int $level, string $message, string $file, int $line)
    {
        parent::__construct($message, 0, null);

        $this->file = $file;

        $this->line = $line;

        $this->level = $level;
    }
}