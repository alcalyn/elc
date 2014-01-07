<?php

namespace EL\PhaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\PhaxBundle\Model\PhaxResponse;
use EL\PhaxBundle\Model\PhaxAction;



class PhaxController extends Controller
{
    
    /**
     * Phax main controller.
     * Call this controller using phax.action() in js.
     * (Define your PhaxConfig.www_script as the route of this controller).
     * 
     * @return \EL\PhaxBundle\Model\PhaxResponse
     */
    public function phaxAction()
    {
        $request = $this->get('request');
        
        $params = $request->request->all();
        
        $controller = $params['phax_metadata']['controller'];
        $action     = $params['phax_metadata']['action'];
        
        $phax_action = new PhaxAction($controller, $action, $params);
        $phax_action
                ->setRequest($request)
                ->setIsCli(false)
        ;
        
        $phax_reaction = $this
                ->get('phax_core')
                ->action($phax_action)
        ;

        return new PhaxResponse($phax_reaction);
    }
}
