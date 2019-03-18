<?php

namespace modules\main\controllers;

use webnick\framework\core\mvc\controllers\WebController;

class MainController extends WebController
{
    public function defaultAction(): string
    {
        return $this->view->render('default');
    }
}