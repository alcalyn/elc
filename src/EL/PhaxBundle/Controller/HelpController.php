<?php

namespace EL\PhaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HelpController extends Controller
{
    public function defaultAction()
    {
        return $this->get('phax')->metaMessage(
            'Phax help message'
        );
    }
    
    public function testAction($params = array())
    {
        return $this->get('phax')->reaction(array(
            'phax_action_metadata' => $params['phax_metadata'],
            $params,
        ));
    }
    
    public function pingAction()
    {
        return $this->get('phax')->metaMessage(
            'pong ('.date('D j M Y G:i:s').')'
        );
    }
}
