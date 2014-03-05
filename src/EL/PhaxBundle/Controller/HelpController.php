<?php

namespace EL\PhaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\PhaxBundle\Model\PhaxAction;

class HelpController extends Controller
{
    
    /**
     * Return a help page in console.log() or console format
     * 
     * @param \EL\PhaxBundle\Model\PhaxAction $phaxAction
     * @return \EL\PhaxBundle\Model\PhaxReaction
     */
    public function defaultAction(PhaxAction $phaxAction)
    {
        $isCli = $phaxAction->isCli();
        
        $pageTemplate = $isCli ?
                'PhaxBundle:Help:help_page.cli.twig' :
                'PhaxBundle:Help:help_page.web.twig' ;
        
        $pageContent = $this
                ->render($pageTemplate)
                ->getContent()
        ;
        
        return $this->get('phax')->metaMessage($pageContent);
    }
    
    
    /**
     * Return all parameters sent in phax action
     * 
     * @param \EL\PhaxBundle\Model\PhaxAction $phaxAction
     * @return \EL\PhaxBundle\Model\PhaxReaction
     */
    public function testAction(PhaxAction $phaxAction)
    {
        $a = 1 / (2 - 2);
        $data = $phaxAction->jsonSerialize();
        $data['phaxAction_metadata'] = $data['phax_metadata'];
        
        return $this->get('phax')->reaction($data);
    }
    
    
    /**
     * Return a pong with datetime
     * 
     * @param \EL\PhaxBundle\Model\PhaxAction $phaxAction
     * @return \EL\PhaxBundle\Model\PhaxReaction
     */
    public function pingAction()
    {
        return $this->get('phax')->metaMessage(
            'pong ('.date('l j F Y, G:i:s').')'
        );
    }
}
