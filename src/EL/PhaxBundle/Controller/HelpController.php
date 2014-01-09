<?php

namespace EL\PhaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\PhaxBundle\Model\PhaxAction;

class HelpController extends Controller
{
    
    /**
     * Return a help page in console.log() or console format
     * 
     * @param \EL\PhaxBundle\Model\PhaxAction $phax_action
     * @return \EL\PhaxBundle\Model\PhaxReaction
     */
    public function defaultAction(PhaxAction $phax_action)
    {
        $is_cli = $phax_action->isCli();
        
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
    
    
    /**
     * Return all parameters sent in phax action
     * 
     * @param \EL\PhaxBundle\Model\PhaxAction $phax_action
     * @return \EL\PhaxBundle\Model\PhaxReaction
     */
    public function testAction(PhaxAction $phax_action)
    {
        $a = 1 / (2 - 2);
        $data = $phax_action->jsonSerialize();
        $data['phax_action_metadata'] = $data['phax_metadata'];
        
        return $this->get('phax')->reaction($data);
    }
    
    
    /**
     * Return a pong with datetime
     * 
     * @param \EL\PhaxBundle\Model\PhaxAction $phax_action
     * @return \EL\PhaxBundle\Model\PhaxReaction
     */
    public function pingAction()
    {
        return $this->get('phax')->metaMessage(
            'pong ('.date('l j F Y, G:i:s').')'
        );
    }
}
