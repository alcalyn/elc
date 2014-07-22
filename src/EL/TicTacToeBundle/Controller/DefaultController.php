<?php

namespace EL\TicTacToeBundle\Controller;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EL\CoreBundle\Event\PartyEvent;
use EL\CoreBundle\Event\PartyRemakeEvent;
use EL\CoreBundle\Entity\Party as CoreParty;
use EL\CoreBundle\Services\PartyService;
use EL\AbstractGameBundle\Model\ELGameAdapter;
use EL\TicTacToeBundle\Form\Type\TicTacToePartyOptionsType;
use EL\TicTacToeBundle\Entity\Party;

class DefaultController extends ELGameAdapter implements EventSubscriberInterface
{
    /**
     * Parties related to core party
     * 
     * @var Party[]
     */
    private $extendedParties = array();
    
    public static function getSubscribedEvents()
    {
        return array(
            'event.party.created'   => 'onPartyCreated',
            'event.party.remake'    => 'onPartyRemake',
        );
    }
    
    public function init()
    {
        $this->get('event_dispatcher')->addSubscriber($this);
    }
    
    public function getPartyType()
    {
        return new TicTacToePartyOptionsType();
    }
    
    public function createParty()
    {
        return new Party();
    }
    
    public function getCreationFormTemplate()
    {
        return implode(':', array(
            'TicTacToeBundle',
            'Default',
            'tictactoeCreationForm.html.twig',
        ));
    }
    
    public function getDisplayOptionsTemplate(CoreParty $coreParty, $extendedParty)
    {
        return implode(':', array(
            'TicTacToeBundle',
            'Default',
            'tictactoeOptions.html.twig',
        ));
    }
    
    public function onPartyCreated(PartyEvent $event)
    {
        $em             = $this->getDoctrine()->getManager();
        $coreParty      = $event->getPartyService()->getParty();
        $extendedParty  = $event->getExtendedParty();
        $party          = clone $extendedParty;
        
        $party->setParty($coreParty);
        $em->persist($party);
        $this->saveParty($coreParty, $party);
    }
    
    public function loadParty(CoreParty $coreParty)
    {
        if (!isset($this->extendedParties[$coreParty->getId()])) {
            $em = $this->getDoctrine()->getManager();

            $party = $em
                    ->getRepository('TicTacToeBundle:Party')
                    ->findOneByCoreParty($coreParty)
            ;
            
            $this->saveParty($coreParty, $party);
        }
        
        return $this->extendedParties[$coreParty->getId()];
    }
    
    private function saveParty(CoreParty $coreParty, Party $party)
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
                'allow_close_slots'     => true,
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
    
    public function getGameLayout()
    {
        return 'TicTacToeBundle::layout.html.twig';
    }
    
    public function activeAction($_locale, PartyService $partyService, $extendedParty)
    {
        $game       = $partyService->getGame();
        $coreParty  = $partyService->getParty();
        
        $this->get('el_core.js_vars')
                ->useTrans('not.your.turn')
        ;
        
        return $this->render('TicTacToeBundle:Default:active.html.twig', array(
            'game'          => $game,
            'party'         => $coreParty,
            'extendedParty' => $extendedParty,
            'gameLayout'    => $this->getGameLayout(),
        ));
    }
    
    public function isMyTurn(PartyService $partyService)
    {
        if ($partyService->getParty()->getState() !== CoreParty::ACTIVE) {
            return false;
        }
        
        $coreParty      = $partyService->getParty();
        $ticTacToeParty = $this->loadParty($coreParty);
        $turn           = $ticTacToeParty->getCurrentPlayer();
        $partyPlayer    = $coreParty->getSlots()->get($turn)->getPlayer();
        $loggedPlayer   = $this->get('el_core.session')->getPlayer();
        
        return $partyPlayer === $loggedPlayer;
    }
    
    public function onPartyRemake(PartyRemakeEvent $event)
    {
        $partyService = $event->getPartyService();
        $newParty = $this->loadParty($partyService->getParty());
        
        // Change first player
        $newParty
                ->setFirstPlayer(1 - $newParty->getFirstPlayer())
                ->setCurrentPlayer($newParty->getFirstPlayer())
                ->setGrid('---------')
                ->setPartyNumber(1)
        ;
    }
}
