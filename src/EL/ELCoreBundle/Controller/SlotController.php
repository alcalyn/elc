<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\ELCoreBundle\Services\PartyService;
use EL\PhaxBundle\Model\PhaxAction;
use EL\PhaxBundle\Model\PhaxResponse;

class SlotController extends Controller
{
    
    
    public function refreshAction(PhaxAction $phaxAction)
    {
        $slugParty  = $phaxAction->slugParty;
        $_locale    = $phaxAction->getLocale();
        
        $party = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $_locale)
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
    
    
    public function openAction(PhaxAction $phaxAction)
    {
        $slugParty  = $phaxAction->slugParty;
        $_locale    = $phaxAction->getLocale();
        $slotIndex  = $phaxAction->slotIndex;
        $slotOpen   = $phaxAction->slotOpen === 'true';
        
        $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $_locale)
                ->openSlot($slotIndex, $slotOpen)
        ;
        
        return $this->refreshAction($phaxAction);
    }
    
    
    public function ajaxJoinAction(PhaxAction $phaxAction)
    {
        $slugParty  = $phaxAction->slugParty;
        $_locale    = $phaxAction->getLocale();
        $slotIndex  = isset($phaxAction->slotIndex) ? intval($phaxAction->slotIndex) : -1 ;
        
        $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $_locale)
                ->join(null, $slotIndex)
        ;
        
        return $this->refreshAction($phaxAction);
    }
    
    
    public function banAction(PhaxAction $phaxAction)
    {
        $slugParty  = $phaxAction->slugParty;
        $_locale    = $phaxAction->getLocale();
        $playerId   = $phaxAction->playerId;
        
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $_locale)
        ;
        
        $ok = $partyService->ban($playerId);
        
        return $this->refreshAction($phaxAction);
    }
    
    
    public function reorderAction(PhaxAction $phaxAction)
    {
        $slugParty  = $phaxAction->slugParty;
        $_locale    = $phaxAction->getLocale();
        $indexes    = $phaxAction->newOrder;
        
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $_locale)
        ;
        
        $partyService->reorderSlots($indexes);
        
        return $this->refreshAction($phaxAction);
    }
}
