<?php

namespace EL\PhaxBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class PhaxService
{
    
    public function render(Controller $controller, $view, array $parameters = array())
    {
        return $controller->render($view, $parameters);
    }
    
}