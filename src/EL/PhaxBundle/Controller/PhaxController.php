<?php

namespace EL\PhaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class PhaxController extends Controller
{
    public function phaxAction($_locale)
    {
        $request = $this->get('request');
        
        $params = null;
        
        if ($request->isMethod('post')) {
            $params = $request->request->all();
        } else {
            $params = $request->query->all();
        }
        
        $params['_locale'] = $_locale;
        
        $controller = $params['phax_controller'];
        $action     = $params['phax_action'];
        
        
        $sf_controller = implode(':', array(
            'ELCoreBundle',
            $controller,
            $action,
        ));
        
        return new JsonResponse(array(
            'ok'        => 'ouais',
            'locale'    => $_locale,
            'method'    => $request->getMethod(),
            'params'    => $params,
            'sf_ctrl'   => $sf_controller,
            'result'    => $this->forward($sf_controller, $params),
        ));
    }
}
