<?php

namespace EL\PhaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\PhaxBundle\Model\PhaxAction;
use EL\PhaxBundle\Model\PhaxResponse;



class SurfController extends Controller
{
	
	/**
     * Return a partial template from path
     * 
     * @param \EL\PhaxBundle\Model\PhaxAction $phax_action
     * @return \EL\PhaxBundle\Model\PhaxReaction
     */
    public function loadTemplateAction(PhaxAction $phax_action)
    {
        $path = $phax_action->get('path');
        $route = $this->get('router')->match($path);
        $forward = $this->forward($route['_controller'], $route);
        
        return $this->get('phax')->reaction(array(
        	'path'		=> $path,
        	'route'		=> $route,
        	'html'		=> $forward->getContent(),
        ));
    }
}
