<?php

namespace EL\Core\EventListener;

use EL\Core\Event\PartyEvent;
use EL\Core\Exception\UserException;

class PartyListener
{
    /**
     * @param \EL\Core\Event\PartyEvent $event
     */
    public function onPartyStartBefore(PartyEvent $event)
    {
        $partyService   = $event->getPartyService();
        $nbPlayerMin    = $partyService->getGame()->getNbplayerMin();
        $nbPlayerMax    = $partyService->getGame()->getNbplayerMax();
        $nbPlayer       = $partyService->getNbPlayer();
        
        if ($nbPlayer < $nbPlayerMin) {
            throw new UserException('cannot.start.notenoughplayer', UserException::TYPE_WARNING);
        }
        
        if ($nbPlayer > $nbPlayerMax) {
            throw new UserException('cannot.start.toomanyplayer', UserException::TYPE_WARNING);
        }
    }
}
