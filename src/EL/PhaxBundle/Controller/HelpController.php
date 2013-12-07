<?php

namespace EL\PhaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HelpController extends Controller
{
    public function defaultAction()
    {
        return $this->get('phax')->reaction(array(
            'help_message' => 'Phax help message',
        ));
    }
    
    public function testAction($params = array())
    {
        return $this->get('phax')->reaction(array(
            'phax_action_metadata' => $params['phax_metadata'],
            $params,
        ));
    }
}
