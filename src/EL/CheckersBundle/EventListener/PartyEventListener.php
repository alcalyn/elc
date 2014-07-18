<?php

namespace EL\CheckersBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EL\CoreBundle\Event\PartyEvent;
use EL\CheckersBundle\Entity\CheckersParty;

class PartyEventListener implements EventSubscriberInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;
    
    /**
     * Constructor
     */
    public function __construct($em)
    {
        $this->em = $em;
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            'event.party.created'   => 'onPartyCreated',
        );
    }
    
    /**
     * @param \EL\CoreBundle\Event\PartyEvent $event
     */
    public function onPartyCreated(PartyEvent $event)
    {
        $coreParty = $event->getPartyService()->getParty();
        $checkersVariant = $event->getExtendedParty();
        
        $checkersParty = new CheckersParty();
        $checkersParty
                ->setParty($coreParty)
                ->setCurrentPlayer($checkersVariant->getFirstPlayer())
                ->setParameters($checkersVariant->getBinaryValue())
        ;
        
        $this->em->persist($checkersParty);
    }
}
