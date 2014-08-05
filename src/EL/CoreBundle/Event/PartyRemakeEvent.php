<?php

namespace EL\CoreBundle\Event;

use EL\CoreBundle\Entity\Party;
use EL\CoreBundle\Services\PartyService;
use EL\AbstractGameBundle\Model\ELGameInterface;

class PartyRemakeEvent extends PartyEvent
{
    /**
     * The event.party.remake.after event is thrown after each time
     * a party has been remade.
     * 
     * The EventListener receives a PartyRemakeEvent instance.
     * 
     * @var string
     */
    const PARTY_REMAKE_AFTER = 'event.party.remake.after';
    
    /**
     * New party which is a remade from the old
     * 
     * @var Party
     */
    protected $oldCoreParty;
    
    /**
     * Constructor
     * 
     * @param \EL\CoreBundle\Services\PartyService $partyService
     * @param \EL\AbstractGameBundle\Model\ELGameInterface $gameInterface
     * @param \stdClass $oldExtendedParty
     * @param Party $oldCoreParty the old core party that the new party has been remade from
     */
    public function __construct(
            PartyService $partyService,
            ELGameInterface $gameInterface = null,
            $oldExtendedParty = null,
            $oldCoreParty = null
    ) {
        parent::__construct($partyService, $gameInterface, $oldExtendedParty);
        
        $this->oldCoreParty = $oldCoreParty;
    }
    
    /**
     * Get the old core party that the new party has been remade from
     * 
     * @return Party
     */
    public function getOldCoreParty()
    {
        return $this->oldCoreParty;
    }
}
