<?php

namespace EL\CoreBundle\EventListener;

use EL\CoreBundle\Event\PartyEvent;

class PartyListener
{
    public function onPartyCreated(PartyEvent $event)
    {
        $partyService = $event->getPartyService();
        $coreParty = $partyService->getParty();
        $gameInterface = $event->getGameInterface();
        $extendedOptions = $event->getExtendedParty();
        
        // Set datetime created
        $coreParty->setDateCreate(new \DateTime());
        
        // notify extended game that party has been created with $extendedOptions options
        $gameInterface->saveParty($coreParty, $extendedOptions);
        
        // get slots configuration from extended party depending of options
        $slotsConfiguration = $gameInterface->getSlotsConfiguration($extendedOptions);
        
        // create slots from given slots configuration
        $partyService->createSlots($slotsConfiguration);
    }
    
    public function onPartyStarted(PartyEvent $event)
    {
        $partyService = $event->getPartyService();
        
        $partyService->start();
    }
    
    public function onPartyActived(PartyEvent $event)
    {
        
    }
    
    public function onPartyEnded(PartyEvent $event)
    {
        
    }
}
