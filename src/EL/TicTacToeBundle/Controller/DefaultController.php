<?php

namespace EL\TicTacToeBundle\Controller;

use EL\AbstractGameBundle\Model\ELGameAdapter;
use EL\CoreBundle\Services\PartyService;
use EL\TicTacToeBundle\Form\Type\TicTacToePartyOptionsType;
use EL\TicTacToeBundle\Entity\Party;
use EL\CoreBundle\Entity\Party as CoreParty;

class DefaultController extends ELGameAdapter
{
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
    
    public function saveParty(CoreParty $coreParty, $extendedParty)
    {
        $em = $this->getDoctrine()->getManager();
        
        $extendedParty->setParty($coreParty);
        
        $em->persist($extendedParty);
        $em->flush();
        
        return true;
    }
    
    public function loadParty(CoreParty $coreParty)
    {
        $em = $this->getDoctrine()->getManager();
        
        $party = $em
                ->getRepository('TicTacToeBundle:Party')
                ->findOneByCoreParty($coreParty)
        ;
        
        return $party;
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
    
    public function createRemake(PartyService $partyService, CoreParty $corePartyClone)
    {
        $em                 = $this->getDoctrine()->getManager();
        $extendedParty      = $partyService->loadExtendedParty();
        $extendedPartyClone = $extendedParty->createRemake($corePartyClone);
        
        $em->persist($extendedPartyClone);
        $em->flush();
    }
}
