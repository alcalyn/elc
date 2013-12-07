<?php

namespace EL\PhaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use EL\PhaxBundle\Model\PhaxResponse;
use EL\PhaxBundle\Model\PhaxException;

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
        
        $params['_locale'] = $_locale;
        
        $controller_name = $params['phax_controller'];
        $action_name     = $params['phax_action'];
        
        
        $service_name = 'phax.'.$controller_name;
        
        if (!$this->has($service_name)) {
            throw new PhaxException(
                    'The controller '.$controller_name.' does not exists.'.
                    'It must be declared as service named '.$service_name
            );
        }
        
        $phax_response = $this
                ->get('phax.'.$controller_name)
                ->{$action_name.'Action'}();
        
        if (!($phax_response instanceof PhaxResponse)) {
            throw new PhaxException(
                    'The controller '.$controller_name.'::'.$action_name.' must return an instance of EL\PhaxBundle\Model\PhaxResponse, '.
                    get_class($phax_response).' returned'
            );
        }
        
        return $phax_response;
    }
}
