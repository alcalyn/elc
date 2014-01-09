<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EL\PhaxBundle\Model\PhaxAction;
use EL\PhaxBundle\Model\PhaxResponse;

class PartyAjaxController extends Controller
{
    
	/**
	 * Create a party
	 * 
     * @param \EL\PhaxBundle\Model\PhaxAction $phax_action
     * @return \EL\PhaxBundle\Model\PhaxReaction
     */
    public function createAction(PhaxAction $phax_action)
    {
    	return $this->get('phax')->reaction(array(
        ));
    }

}
