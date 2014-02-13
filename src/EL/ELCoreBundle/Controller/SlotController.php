<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\ELCoreBundle\Services\PartyService;
use EL\PhaxBundle\Model\PhaxAction;
use EL\PhaxBundle\Model\PhaxResponse;

class SlotController extends Controller
{
    
    
    public function refreshAction(PhaxAction $phax_action)
    {
        $slug_party = $phax_action->slug_party;
        $_locale    = $phax_action->getLocale();
        
        $party = $this
                ->get('el_core.party')
                ->setPartyBySlug($slug_party, $_locale)
                ->getParty()
        ;
        
        $slots = array();
        
        foreach ($party->getSlots() as $slot) {
            $slots[$slot->getPosition() - 1] = $slot->jsonSerialize();
        }
        
        return $this->get('phax')->reaction(array(
            'party'     => $party->jsonSerialize(),
            'slots'     => $slots,
        ));
    }
    
    
    public function openAction(PhaxAction $phax_action)
    {
        $slug_party = $phax_action->slug_party;
        $_locale    = $phax_action->getLocale();
        $slot_index = $phax_action->slot_index;
        $slot_open  = $phax_action->slot_open === 'true';
        
        $party_service = $this
                ->get('el_core.party')
                ->setPartyBySlug($slug_party, $_locale)
                ->openSlot($slot_index, $slot_open)
        ;
        
        return $this->refreshAction($phax_action);
    }
    
    
    public function ajaxJoinAction(PhaxAction $phax_action)
    {
        $slug_party = $phax_action->slug_party;
        $_locale    = $phax_action->getLocale();
        $slot_index = isset($phax_action->slot_index) ? intval($phax_action->slot_index) : -1 ;
        
        $this
                ->get('el_core.party')
                ->setPartyBySlug($slug_party, $_locale)
                ->join(null, $slot_index)
        ;
        
        return $this->refreshAction($phax_action);
    }
    
    
    public function banAction(PhaxAction $phax_action)
    {
        $slug_party = $phax_action->slug_party;
        $_locale    = $phax_action->getLocale();
        $player_id  = $phax_action->player_id;
        
        $party_service = $this
                ->get('el_core.party')
                ->setPartyBySlug($slug_party, $_locale)
        ;
        
        $ok = $party_service->ban($player_id);
        
        return $this->refreshAction($phax_action);
    }
    
    
    public function reorderAction(PhaxAction $phax_action)
    {
        $slug_party = $phax_action->slug_party;
        $_locale    = $phax_action->getLocale();
        $indexes    = $phax_action->new_order;
        
        $party_service = $this
                ->get('el_core.party')
                ->setPartyBySlug($slug_party, $_locale)
        ;
        
        $party_service->reorderSlots($indexes);
        
        return $this->refreshAction($phax_action);
    }
}
