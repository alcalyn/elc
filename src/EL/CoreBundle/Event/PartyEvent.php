<?php

namespace EL\CoreBundle\Event;

use EL\CoreBundle\Services\PartyService;
use EL\AbstractGameBundle\Model\ELGameInterface;

class PartyEvent extends ELCoreEvent
{
    /**
     * The event.party.created event is thrown each time
     * a party is created in the system.
     * 
     * The EventListener receives a PartyEvent instance.
     * 
     * @var string
     */
    const PARTY_CREATED = 'event.party.created';
    
    /**
     * The event.party.started event is thrown each time
     * a party is started, and is waiting a few seconds before being actived.
     * 
     * The EventListener receives a PartyEvent instance.
     * 
     * @var string
     */
    const PARTY_STARTED = 'event.party.started';
    
    /**
     * The event.party.actived event is thrown each time
     * a party is actived, and players can now play the party.
     * 
     * The EventListener receives a PartyEvent instance.
     * 
     * @var string
     */
    const PARTY_ACTIVED = 'event.party.actived';
    
    /**
     * The event.party.ended event is thrown each time
     * a party is ended, and players can no longer play on this party.
     * 
     * The EventListener receives a PartyEvent instance.
     * 
     * @var string
     */
    const PARTY_ENDED = 'event.party.ended';
    
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
    protected $extendedParty;
    
    /**
     * Constructor
     * 
     * @param PartyService $partyService
     * @param \stdClass $extParty
     */
    public function __construct(PartyService $partyService, ELGameInterface $gameInterface = null, $extParty = null)
    {
        $this->partyService = $partyService;
        $this->gameInterface = $gameInterface;
        $this->extendedParty = $extParty;
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
    public function getExtendedParty()
    {
        return $this->extendedParty;
    }
}
