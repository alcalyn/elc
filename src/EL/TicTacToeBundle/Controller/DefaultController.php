<?php

namespace EL\TicTacToeBundle\Controller;

use EL\AbstractGameBundle\Model\ELGameAdapter;
use EL\CoreBundle\Services\PartyService;
use EL\TicTacToeBundle\Form\Type\TicTacToePartyOptionsType;
use EL\TicTacToeBundle\Entity\Party;
use EL\CoreBundle\Entity\Party as CoreParty;

class DefaultController extends ELGameAdapter
{
    public function getOptionsType()
    {
        return new TicTacToePartyOptionsType();
    }
    
    public function getOptions()
    {
        return new Party();
    }
    
    public function saveOptions(CoreParty $coreParty, $options)
    {
        $em = $this->getDoctrine()->getManager();
        
        $options->setParty($coreParty);
        
        $em->persist($options);
        $em->flush();
        
        return true;
    }
    
    public function loadOptions(CoreParty $coreParty)
    {
        $em = $this->getDoctrine()->getManager();
        
        $party = $em
                ->getRepository('TicTacToeBundle:Party')
                ->findOneBySlugParty($coreParty->getSlug())
        ;
        
        $options = new Party();
        
        $options->setFirstPlayer($party->getFirstPlayer());
        
        return $options;
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
    
    public function loadParty($slugParty)
    {
        $em = $this->getDoctrine()->getManager();
        
        $party = $em
                ->getRepository('TicTacToeBundle:Party')
                ->findOneBySlugParty($slugParty);
        
        return $party;
    }
    
    public function activeAction($_locale, PartyService $partyService)
    {
        $em = $this->getDoctrine()->getManager();
        
        $game       = $partyService->getGame();
        $coreParty  = $partyService->getParty();

        $party = $em
                ->getRepository('TicTacToeBundle:Party')
                ->findOneByCoreParty($coreParty)
        ;
        
        return $this->render('TicTacToeBundle:Default:active.html.twig', array(
            'game'          => $game,
            'party'         => $coreParty,
            'extendedParty' => $party,
        ));
    }
    
    public function isMyTurn(PartyService $partyService)
    {
        if ($partyService->getParty()->getState() !== CoreParty::ACTIVE) {
            return false;
        }
        
        $coreParty      = $partyService->getParty();
        $ticTacToeParty = $this->loadParty($coreParty->getSlug());
        $turn           = $ticTacToeParty->getCurrentPlayer();
        $partyPlayer    = $coreParty->getSlots()->get($turn)->getPlayer();
        $loggedPlayer   = $this->get('el_core.session')->getPlayer();
        
        return $partyPlayer === $loggedPlayer;
    }
    
    public function createRemake($slugParty, CoreParty $corePartyClone)
    {
        $em = $this->getDoctrine()->getManager();
        
        $extendedParty = $em
                ->getRepository('TicTacToeBundle:Party')
                ->findOneBySlugParty($slugParty)
        ;
        
        $extendedPartyClone = $extendedParty->createRemake($corePartyClone);
        
        $em->persist($extendedPartyClone);
        $em->flush();
    }
}
