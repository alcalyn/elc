<?php

namespace EL\PhaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\PhaxBundle\Model\PhaxAction;

class SurfController extends Controller
{
    /**
     * Return a partial template from path
     * 
     * @param \EL\PhaxBundle\Model\PhaxAction $phaxAction
     * @return \EL\PhaxBundle\Model\PhaxReaction
     */
    public function loadTemplateAction(PhaxAction $phaxAction)
    {
        $path       = $phaxAction->get('path');
        $route      = $this->get('router')->match($path);
        $forward    = $this->forward($route['_controller'], $route);
        $data       = json_decode($forward->getContent());
        
        return $this->get('phax')->reaction(array(
            'path'        => $path,
            'data'        => $data,
        ));
    }
}
