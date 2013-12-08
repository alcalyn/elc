<?php

namespace EL\PhaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\PhaxBundle\Model\PhaxResponse;



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
        
        $params['phax_metadata'] += array(
            '_locale'       => $request->getLocale(),
            'mode_cli'      => false,
        );
        
        $controller_name    = $params['phax_metadata']['controller'];
        $action_name        = $params['phax_metadata']['action'];
        
        $phax_reaction = $this
                ->get('phax_core')
                ->action($controller_name, $action_name, $params);
        
        return new PhaxResponse($phax_reaction);
    }
}
