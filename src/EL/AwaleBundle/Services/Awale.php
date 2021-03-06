<?php

namespace EL\AwaleBundle\Services;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EL\CoreBundle\Event\PartyEvent;
use EL\CoreBundle\Entity\Party;
use EL\CoreBundle\Services\PartyService;
use EL\AbstractGameBundle\Model\ELGameAdapter;
use EL\AwaleBundle\Form\Type\AwalePartyType;
use EL\AwaleBundle\Entity\AwaleParty;

class Awale extends ELGameAdapter implements EventSubscriberInterface
{
    /**
     * Parties related to core party
     * 
     * @var AwaleParty[]
     */
    private $extendedParties = array();
    
    public static function getSubscribedEvents()
    {
        return array(
            PartyEvent::PARTY_CREATE_BEFORE => 'onPartyCreateBefore',
        );
    }
    
    public function init()
    {
        $this->get('event_dispatcher')->addSubscriber($this);
    }
    
    public function getPartyType()
    {
        return new AwalePartyType();
    }
    
    public function createStandardOptions()
    {
        return new AwaleParty();
    }
    
    public function onPartyCreateBefore(PartyEvent $event)
    {
        $em                 = $this->getDoctrine()->getManager();
        $coreParty          = $event->getPartyService()->getParty();
        $awaleParty         = $event->getExtendedOptions();             /* @var $awaleParty AwaleParty */
        $awaleCore          = $this->get('awale.core');
        $seedsPerContainer  = $awaleParty->getSeedsPerContainer();
        
        $awaleParty
                ->setParty($coreParty)
                ->setGrid($awaleCore->fillGrid($seedsPerContainer))
        ;
        
        $em->persist($awaleParty);
        $this->saveParty($coreParty, $awaleParty);
    }
    
    public function loadParty(Party $coreParty)
    {
        if (!isset($this->extendedParties[$coreParty->getId()])) {
            $em = $this->getDoctrine()->getManager();

            $party = $em
                    ->getRepository('AwaleBundle:AwaleParty')
                    ->findOneBy(array(
                        'party' => $coreParty,
                    ))
            ;
            
            $this->saveParty($coreParty, $party);
        }
        
        return $this->extendedParties[$coreParty->getId()];
    }
    
    private function saveParty(Party $coreParty, AwaleParty $party)
    {
        $this->extendedParties[$coreParty->getId()] = $party;
    }
    
    public function getSlotsConfiguration($options)
    {
        return array(
            'parameters' => array(
                'allow_add_slots'       => false,
                'allow_remove_slots'    => false,
                'min_slots_number'      => 2,
                'max_slots_number'      => 2,
                'allow_reorder_slots'   => true,
                'allow_close_slots'     => false,
                'allow_invite_cpu'      => false,
            ),
            'slots' => array(
                array(
                    'open'      => true,
                    'host'      => true,
                    'score'     => 0,
                ),
                array(
                    'open'      => true,
                    'score'     => 0,
                ),
            ),
        );
    }
    
    public function getDisplayOptionsTemplate(Party $coreParty, $extendedParty)
    {
        return 'AwaleBundle:Awale:displayOptions.html.twig';
    }
    
    public function getGameLayout()
    {
        return 'AwaleBundle::layout.html.twig';
    }
    
    /**
     * @param string $_locale
     * @param \EL\CoreBundle\Services\PartyService $partyService
     * @param AwaleParty $extendedParty
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function activeAction($_locale, PartyService $partyService, $extendedParty)
    {
        $awaleCore      = $this->get('awale.core');             /* @var $awaleCore     AwaleCore  */
        $coreParty      = $partyService->getParty();            /* @var $coreParty     Party      */
        $sessionPlayer  = $this->get('el_core.session')->getPlayer();
        $slot1Player    = $coreParty->getSlots()->get(1)->getPlayer();
        $reverse        = $sessionPlayer->getId() === $slot1Player->getId();
        
        $this->get('el_core.js_vars')
                ->useTrans('not.your.turn')
                ->useTrans('container.is.empty')
                ->useTrans('seeds.per.container')
                ->useTrans('feed.the.opponent')
        ;
        
        return $this->render('AwaleBundle:Awale:active.html.twig', array(
            'game'          => $partyService->getGame(),
            'coreParty'     => $coreParty,
            'gameLayout'    => $this->getGameLayout(),
            'reverse'       => $reverse,
            'grid'          => $awaleCore->unserializeGrid($extendedParty->getGrid()),
            'seedsToWin'    => $awaleCore->getSeedsNeededToWin($extendedParty->getSeedsPerContainer()),
        ));
    }
    
    public function getOptions($oldParty)
    {
        $party = new AwaleParty();
        
        return $party
                ->setSeedsPerContainer($oldParty->getSeedsPerContainer())
        ;
    }
}
