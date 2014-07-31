<?php

namespace EL\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phax\CoreBundle\Model\PhaxAction;
use EL\CoreBundle\Entity\Party;

class SlotController extends Controller
{
    /**
     * Return a response with an updated party instance
     * 
     * @param \EL\CoreBundle\Entity\Party $party
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
        $slugGame   = $phaxAction->slugGame;
        $_locale    = $phaxAction->getLocale();
        
        $party = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $slugGame, $_locale)
                ->getParty()
        ;
        
        return $this->party($party);
    }
    
    
    public function openAction(PhaxAction $phaxAction)
    {
        $em         = $this->getDoctrine()->getManager();
        $slugParty  = $phaxAction->slugParty;
        $slugGame   = $phaxAction->slugGame;
        $_locale    = $phaxAction->getLocale();
        $slotIndex  = $phaxAction->slotIndex;
        $slotOpen   = $phaxAction->slotOpen === 'true';
        
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $slugGame, $_locale)
                ->openSlot($slotIndex, $slotOpen)
        ;
        
        return $this->party($partyService->getParty());
    }
    
    
    public function ajaxJoinAction(PhaxAction $phaxAction)
    {
        $em         = $this->getDoctrine()->getManager();
        $slugParty  = $phaxAction->slugParty;
        $slugGame   = $phaxAction->slugGame;
        $_locale    = $phaxAction->getLocale();
        $slotIndex  = isset($phaxAction->slotIndex) ? intval($phaxAction->slotIndex) : -1 ;
        
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $slugGame, $_locale)
                ->join(null, $slotIndex)
        ;
        
        return $this->party($partyService->getParty());
    }
    
    
    public function banAction(PhaxAction $phaxAction)
    {
        $em         = $this->getDoctrine()->getManager();
        $slugParty  = $phaxAction->slugParty;
        $slugGame   = $phaxAction->slugGame;
        $_locale    = $phaxAction->getLocale();
        $playerId   = $phaxAction->playerId;
        
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $slugGame, $_locale)
                ->ban($playerId)
        ;
        
        return $this->party($partyService->getParty());
    }
    
    
    public function reorderAction(PhaxAction $phaxAction)
    {
        $slugParty  = $phaxAction->slugParty;
        $slugGame   = $phaxAction->slugGame;
        $_locale    = $phaxAction->getLocale();
        $indexes    = $phaxAction->newOrder;
        
        $partyService = $this
                ->get('el_core.party')
                ->setPartyBySlug($slugParty, $slugGame, $_locale)
                ->reorderSlots($indexes)
        ;
        
        return $this->party($partyService->getParty());
    }
}
