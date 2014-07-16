<?php

namespace EL\CoreBundle\EventListener;

use EL\CoreBundle\Event\PartyEvent;

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
        $partyService = $event->getPartyService();
        
        $partyService->start();
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
