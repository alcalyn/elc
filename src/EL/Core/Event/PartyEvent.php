<?php

namespace EL\Core\Event;

use EL\Core\Service\PartyService;
use EL\Bundle\CoreBundle\AbstractGame\Model\ELGameInterface;

class PartyEvent extends Event
{
    /**
     * The event.party.create.before event is thrown before
     * the system create a party.
     * 
     * The EventListener receives a PartyEvent instance.
     * 
     * @var string
     */
    const PARTY_CREATE_BEFORE = 'event.party.create.before';
    
    /**
     * The event.party.create.after event is thrown each time
     * a party is created in the system.
     * 
     * The EventListener receives a PartyEvent instance.
     * 
     * @var string
     */
    const PARTY_CREATE_AFTER = 'event.party.create.after';
    
    /**
     * The event.party.start.before event is thrown before
     * a party is started, and is waiting a few seconds before being actived.
     * 
     * The EventListener receives a PartyEvent instance.
     * 
     * @var string
     */
    const PARTY_START_BEFORE = 'event.party.start.before';
    
    /**
     * The event.party.start.before event is thrown after
     * a party has been started, and is waiting a few seconds before being actived.
     * 
     * The EventListener receives a PartyEvent instance.
     * 
     * @var string
     */
    const PARTY_START_AFTER = 'event.party.start.after';
    
    /**
     * The event.party.actived event is thrown when a party will be actived.
     * 
     * The EventListener receives a PartyEvent instance.
     * 
     * @var string
     */
    const PARTY_ACTIVE_BEFORE = 'event.party.active.before';
    
    /**
     * The event.party.actived event is thrown
     * when a party has been actived, players can now see and play the game.
     * 
     * The EventListener receives a PartyEvent instance.
     * 
     * @var string
     */
    const PARTY_ACTIVE_AFTER = 'event.party.active.after';
    
    /**
     * The event.party.ended event is thrown before each time
     * a party is ended, and players can no longer play on this party.
     * 
     * The EventListener receives a PartyEvent instance.
     * 
     * @var string
     */
    const PARTY_END_BEFORE = 'event.party.end.before';
    
    /**
     * The event.party.ended event is thrown after each time
     * a party is ended, and players can no longer play on this party.
     * 
     * The EventListener receives a PartyEvent instance.
     * 
     * @var string
     */
    const PARTY_END_AFTER = 'event.party.end.after';
    
    /**
     * The event.party.remake.before event is thrown before each time
     * a party has been remade.
     * 
     * The EventListener receives a PartyEvent instance.
     * 
     * @var string
     */
    const PARTY_REMAKE_BEFORE = 'event.party.remake.before';
    
    /**
     * @var PartyService
     */
    protected $partyService;
    
    /**
     * @var ELGameInterface
     */
    protected $gameInterface;
    
    /**
     * @var \stdClass
     */
    protected $extendedOptions;
    
    /**
     * Constructor
     * 
     * @param \EL\Core\Service\PartyService $partyService
     * @param \EL\Bundle\CoreBundle\AbstractGame\Model\ELGameInterface $gameInterface
     * @param \stdClass $extOptions
     */
    public function __construct(PartyService $partyService, ELGameInterface $gameInterface = null, $extOptions = null)
    {
        $this->partyService = $partyService;
        $this->gameInterface = $gameInterface;
        $this->extendedOptions = $extOptions;
    }
    
    /**
     * @return PartyService
     */
    public function getPartyService()
    {
        return $this->partyService;
    }
    
    /**
     * @return ELGameInterface
     */
    public function getGameInterface()
    {
        return $this->gameInterface;
    }
    
    /**
     * @return \stdClass
     */
    public function getExtendedOptions()
    {
        return $this->extendedOptions;
    }
}
