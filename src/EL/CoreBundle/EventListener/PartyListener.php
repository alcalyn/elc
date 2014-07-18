<?php

namespace EL\CoreBundle\EventListener;

use EL\CoreBundle\Event\PartyEvent;
use EL\CoreBundle\Exception\ELUserException;

class PartyListener
{
    /**
     * @param \EL\CoreBundle\Event\PartyEvent $event
     */
    public function onPartyCreated(PartyEvent $event)
    {
        
    }
    
    /**
     * @param \EL\CoreBundle\Event\PartyEvent $event
     */
    public function onPartyStarted(PartyEvent $event)
    {
        $partyService   = $event->getPartyService();
        $nbPlayerMin    = $partyService->getGame()->getNbplayerMin();
        $nbPlayerMax    = $partyService->getGame()->getNbplayerMax();
        $nbPlayer       = $partyService->getNbPlayer();
        
        if ($nbPlayer < $nbPlayerMin) {
            throw new ELUserException('cannot.start.notenoughplayer', -1, ELUserException::TYPE_WARNING);
        }
        
        if ($nbPlayer > $nbPlayerMax) {
            throw new ELUserException('cannot.start.toomanyplayer', -1, ELUserException::TYPE_WARNING);
        }
    }
    
    /**
     * @param \EL\CoreBundle\Event\PartyEvent $event
     */
    public function onPartyActived(PartyEvent $event)
    {
        
    }
    
    /**
     * @param \EL\CoreBundle\Event\PartyEvent $event
     */
    public function onPartyEnded(PartyEvent $event)
    {
        
    }
}
