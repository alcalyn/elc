<?php

namespace EL\AwaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phax\CoreBundle\Model\PhaxAction;
use EL\CoreBundle\Services\PartyService;
use EL\AwaleBundle\Entity\AwaleParty;
use EL\AwaleBundle\Services\AwaleCore;

class AwaleController extends Controller
{
    /**
     * Refresh action
     * 
     * @return \Phax\CoreBundle\Model\PhaxReaction
     */
    public function refreshAction(PhaxAction $phaxAction, $slugParty)
    {
        $awaleCore      = $this->get('awale.core');             /* @var $awaleCore AwaleCore */
        $partyService   = $this->get('el_core.party');          /* @var $partyService PartyService */
        
        $partyService->setPartyBySlug($slugParty, 'awale', $phaxAction->getLocale(), $this->container);
        
        $extendedParty  = $partyService->loadExtendedParty();   /* @var $extendedParty AwaleParty */
        $grid           = $awaleCore->unserializeGrid($extendedParty->getGrid());
        
        return $this->get('phax')->reaction(array(
            'grid' => $grid,
        ));
    }
    
    /**
     * Play action
     * 
     * @return \Phax\CoreBundle\Model\PhaxReaction
     */
    public function playAction(PartyService $partyService, $row, $col)
    {
    }
}
