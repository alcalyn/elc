<?php

namespace EL\CoreBundle\EventListener;

use EL\CoreBundle\Event\PartyEvent;
use EL\CoreBundle\Exception\ELUserException;

class PartyListener
{
    /**
     * @param \EL\CoreBundle\Event\PartyEvent $event
     */
    public function onPartyStartBefore(PartyEvent $event)
    {
        $partyService   = $event->getPartyService();
        $nbPlayerMin    = $partyService->getGame()->getNbplayerMin();
        $nbPlayerMax    = $partyService->getGame()->getNbplayerMax();
        $nbPlayer       = $partyService->getNbPlayer();
        
        if ($nbPlayer < $nbPlayerMin) {
            throw new ELUserException('cannot.start.notenoughplayer', ELUserException::TYPE_WARNING);
        }
        
        if ($nbPlayer > $nbPlayerMax) {
            throw new ELUserException('cannot.start.toomanyplayer', ELUserException::TYPE_WARNING);
        }
    }
}
