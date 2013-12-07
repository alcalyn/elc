<?php

namespace EL\PhaxBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\PhaxBundle\Model\PhaxResponse;


class PhaxService
{
    
    public function response(array $parameters = array())
    {
        return new PhaxResponse($parameters);
    }
    
    public function render(Controller $controller, $view, array $parameters = array())
    {
        return $controller->render($view, $parameters);
    }
    
    
    
}