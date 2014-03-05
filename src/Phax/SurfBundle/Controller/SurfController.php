<?php

namespace Phax\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phax\CoreBundle\Model\PhaxAction;

class SurfController extends Controller
{
    /**
     * Return a partial template from path
     * 
     * @param \Phax\CoreBundle\Model\PhaxAction $phaxAction
     * @return \Phax\CoreBundle\Model\PhaxReaction
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
