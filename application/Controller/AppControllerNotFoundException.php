<?php

namespace Controller;

/**
 * Classe para gerênciamento das exceptions lançadas pelos
 * controllers.
 */
class AppControllerNotFoundException extends \AppException
{
    private $controller;

    public function __construct(AppController $controller)
    {
        $this->controller = $controller;
    }

    public function getController()
    {
        return $this->controller;
    }
}
