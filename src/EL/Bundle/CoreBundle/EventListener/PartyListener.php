<?php

namespace EL\Bundle\CoreBundle\EventListener;

use EL\Bundle\CoreBundle\Event\PartyEvent;
use EL\Bundle\CoreBundle\Exception\ELUserException;

class PartyListener
{
    /**
     * @param \EL\Bundle\CoreBundle\Event\PartyEvent $event
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
