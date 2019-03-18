<?php

namespace webnick\framework\core\mvc;

use webnick\framework\helpers\PathHelper;
use webnick\framework\core\Config;

/**
 * Class Layout класс для работы с шаблоном (макетом)
 *
 * @package framework\core\mvc
 */
class Layout
{
    public $file = 'default';

    protected $title;

    protected $content;

    public function __construct()
    {
        $this->setTitle('');
    }

    /**
     * Установить заголовок страницы
     *
     * @param string $title
     * @return string
     */
    public function setTitle(string $title): string
    {
        $config = Config::getConfig('layout')['title'];

        return $this->title = trim($title . $config['separator'] . $config['name'], $config['separator']);
    }

    /**
     * Отобразить шаблон
     */
    public function render(?string &$content = ''): void
    {
        $this->content = $content;

        unset($content);

        if ($this->file)
            require PathHelper::getPath('app') . '/layouts/' . $this->file . ".php";
    }
}