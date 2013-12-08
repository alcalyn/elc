<?php

namespace EL\PhaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HelpController extends Controller
{
    public function defaultAction($params = array())
    {
        $is_cli = $params['phax_metadata']['mode_cli'];
        
        $page_template = $is_cli ?
                'PhaxBundle:Help:help_page.cli.twig' :
                'PhaxBundle:Help:help_page.web.twig' ;
        
        $page_variables = array();
        
        $page_content = $this
                ->render($page_template, $page_variables)
                ->getContent()
        ;
        
        return $this->get('phax')->metaMessage($page_content);
    }
    
    public function testAction($params = array())
    {
        $params['phax_action_metadata'] = $params['phax_metadata'];
        
        return $this->get('phax')->reaction($params);
    }
    
    public function pingAction()
    {
        return $this->get('phax')->metaMessage(
            'pong ('.date('l j F Y, G:i:s').')'
        );
    }
}
