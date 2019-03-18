<?php

namespace webnick\framework\core\mvc\controllers;

/**
 * Class ConsoleController базовый класс консольного контроллера
 *
 * @package framework\core\mvc\controllers
 */
class ConsoleController extends Controller
{
    /**
     * Отправить сообщение на консоль
     *
     * @param string $message
     */
    public static function sendMessage(string $message): void
    {
        echo $message . '. Date: ' . date('d.m.Y H:i:s') . "\r\n";
    }
}