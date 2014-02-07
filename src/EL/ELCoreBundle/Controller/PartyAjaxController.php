<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\PhaxBundle\Model\PhaxAction;
use EL\PhaxBundle\Model\PhaxResponse;
use EL\ELCoreBundle\Model\ELCoreException;

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
    
    /**
     * Return party instance from slug
     * 
     * @param \EL\PhaxBundle\Model\PhaxAction $phax_action
     * @return \EL\PhaxBundle\Model\PhaxReaction
     */
    public function refreshAction(PhaxAction $phax_action)
    {
        $locale     = $phax_action->get('locale');
        $slug_party = $phax_action->get('slug_party');
        
        if (is_null($locale)) {
            throw new ELCoreException(
                'partyAjax::refreshAction : locale must be defined'
            );
        }
        
        if (is_null($slug_party)) {
            throw new ELCoreException(
                'partyAjax::refreshAction : slug_party must be defined'
            );
        }
        
        $party_service = $this
                ->get('el_core.party')
                ->setPartyBySlug($slug_party, $locale)
        ;
        
        return $this->get('phax')->reaction(array(
            'core_party' => $party_service->getParty()->jsonSerialize(),
        ));
    }
}
