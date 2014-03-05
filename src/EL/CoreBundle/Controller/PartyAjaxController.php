<?php

namespace EL\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phax\CoreBundle\Model\PhaxAction;
use Phax\CoreBundle\Model\PhaxResponse;
use EL\CoreBundle\Model\ELCoreException;

class PartyAjaxController extends Controller
{
    /**
     * Create a party
     * 
     * @param \Phax\CoreBundle\Model\PhaxAction $phaxAction
     * @return \Phax\CoreBundle\Model\PhaxReaction
     */
    public function createAction(PhaxAction $phaxAction)
    {
        return $this->get('phax')->reaction(array(
        ));
    }
    
    /**
     * Return party instance from slug
     * 
     * @param \Phax\CoreBundle\Model\PhaxAction $phaxAction
     * @return \Phax\CoreBundle\Model\PhaxReaction
     */
    public function refreshAction(PhaxAction $phaxAction)
    {
        $locale     = $phaxAction->get('locale');
        $slugParty  = $phaxAction->get('slugParty');
        
        if (is_null($locale)) {
            throw new ELCoreException(
                'partyAjax::refreshAction : locale must be defined'
            );
        }
        
        if (is_null($slugParty)) {
            throw new ELCoreException(
                'partyAjax::refreshAction : slugParty must be defined'
            );
        }
        
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $locale)
        ;
        
        return $this->get('phax')->reaction(array(
            'coreParty' => $partyService->getParty()->jsonSerialize(),
        ));
    }
}
