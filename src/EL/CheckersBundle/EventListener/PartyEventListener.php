<?php

namespace EL\CheckersBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EL\CoreBundle\Event\PartyEvent;
use EL\CheckersBundle\Checkers\Variant;
use EL\CheckersBundle\Checkers\Move;
use EL\CheckersBundle\Entity\CheckersParty;
use EL\CheckersBundle\Services\Checkers;

class PartyEventListener implements EventSubscriberInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;
    
    /**
     * @var Checkers
     */
    protected $checkers;
    
    /**
     * Constructor
     */
    public function __construct($em, Checkers $checkers)
    {
        $this->em = $em;
        $this->checkers = $checkers;
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            PartyEvent::PARTY_CREATE_BEFORE => 'onPartyCreateBefore',
            PartyEvent::PARTY_ACTIVE_AFTER  => 'onPartyActiveAfter',
        );
    }
    
    /**
     * @param \EL\CoreBundle\Event\PartyEvent $event
     */
    public function onPartyCreateBefore(PartyEvent $event)
    {
        $coreParty = $event->getPartyService()->getParty();
        $checkersVariant = $event->getExtendedOptions();        /* @var $checkersVariant Variant */
        
        $checkersParty = new CheckersParty();
        $checkersParty
                ->setParty($coreParty)
                ->setCurrentPlayer($checkersVariant->getFirstPlayer())
                ->setParameters($checkersVariant->getBinaryValue())
        ;
        
        $this->em->persist($checkersParty);
    }
    
    /**
     * @param \EL\CoreBundle\Event\PartyEvent $event
     */
    public function onPartyActiveAfter(PartyEvent $event)
    {
        $partyService   = $event->getPartyService();
        $checkersParty  = $partyService->loadExtendedParty();           /* @var $checkersParty CheckersParty */
        $variant        = new Variant($checkersParty->getParameters());
        $grid           = $this->checkers->initGrid($variant);
        $move           = new Move(0);
        
        $checkersParty
                ->setGrid($grid)
                ->setLastMove(json_encode($move))
        ;
    }
}
