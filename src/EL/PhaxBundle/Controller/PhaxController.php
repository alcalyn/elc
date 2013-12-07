<?php

namespace EL\PhaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\PhaxBundle\Model\PhaxReaction;
use EL\PhaxBundle\Model\PhaxException;
use EL\PhaxBundle\Model\PhaxResponse;

class PhaxController extends Controller
{
    public function phaxAction()
    {
        $request = $this->get('request');
        $_locale = $request->getLocale();
        
        $params = null;
        
        if ($request->isMethod('post')) {
            $params = $request->request->all();
        } else {
            $params = $request->query->all();
        }
        
        $params['phax_metadata'] += array(
            '_locale'       => $_locale,
            'mode_cli'      => false,
        );
        
        $controller_name    = $params['phax_metadata']['controller'];
        $action_name        = $params['phax_metadata']['action'];
        $service_name       = 'phax.'.$controller_name;
        
        if (!$this->has($service_name)) {
            throw new PhaxException(
                    'The controller '.$controller_name.' does not exists. '.
                    'It must be declared as service named '.$service_name
            );
        }
        
        $phax_reaction = $this
                ->get($service_name)
                ->{$action_name.'Action'}($params);
        
        if (!($phax_reaction instanceof PhaxReaction)) {
            throw new PhaxException(
                    'The controller '.$controller_name.'::'.$action_name.' must return an instance of EL\PhaxBundle\Model\PhaxReaction, '.
                    get_class($phax_reaction).' returned'
            );
        }
        
        return new PhaxResponse($phax_reaction);
    }
}
