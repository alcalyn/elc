<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\PhaxBundle\Model\PhaxAction;
use EL\ELCoreBundle\Entity\Party;

class SlotController extends Controller
{
    /**
     * Return a response with an updated party instance
     * 
     * @param \EL\ELCoreBundle\Entity\Party $party
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function party(Party $party)
    {
        return $this->get('phax')->reaction(array(
            'party'     => $party->jsonSerialize(),
        ));
    }
    
    public function refreshAction(PhaxAction $phaxAction)
    {
        $slugParty  = $phaxAction->slugParty;
        $_locale    = $phaxAction->getLocale();
        
        $party = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $_locale)
                ->getParty()
        ;
        
        return $this->party($party);
    }
    
    
    public function openAction(PhaxAction $phaxAction)
    {
        $slugParty  = $phaxAction->slugParty;
        $_locale    = $phaxAction->getLocale();
        $slotIndex  = $phaxAction->slotIndex;
        $slotOpen   = $phaxAction->slotOpen === 'true';
        
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $_locale)
                ->openSlot($slotIndex, $slotOpen)
        ;
        
        return $this->party($partyService->getParty());
    }
    
    
    public function ajaxJoinAction(PhaxAction $phaxAction)
    {
        $slugParty  = $phaxAction->slugParty;
        $_locale    = $phaxAction->getLocale();
        $slotIndex  = isset($phaxAction->slotIndex) ? intval($phaxAction->slotIndex) : -1 ;
        
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $_locale)
                ->join(null, $slotIndex)
        ;
        
        return $this->party($partyService->getParty());
    }
    
    
    public function banAction(PhaxAction $phaxAction)
    {
        $slugParty  = $phaxAction->slugParty;
        $_locale    = $phaxAction->getLocale();
        $playerId   = $phaxAction->playerId;
        
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $_locale)
                ->ban($playerId)
        ;
        
        return $this->party($partyService->getParty());
    }
    
    
    public function reorderAction(PhaxAction $phaxAction)
    {
        $slugParty  = $phaxAction->slugParty;
        $_locale    = $phaxAction->getLocale();
        $indexes    = $phaxAction->newOrder;
        
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $_locale)
                ->reorderSlots($indexes)
        ;
        
        return $this->party($partyService->getParty());
    }
}
