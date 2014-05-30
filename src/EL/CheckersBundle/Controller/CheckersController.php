<?php

namespace EL\CheckersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phax\CoreBundle\Model\PhaxAction;
use EL\CoreBundle\Exception\ELCoreException;
use EL\CoreBundle\Entity\Party;
use EL\CoreBundle\Util\Coords;
use EL\CoreBundle\Services\PartyService;
use EL\CheckersBundle\Entity\CheckersParty;
use EL\CheckersBundle\Checkers\CheckersIllegalMoveException;
use EL\CheckersBundle\Checkers\Move;

class CheckersController extends Controller
{
    /**
     * A player moved a piece
     * 
     * @param \Phax\CoreBundle\Model\PhaxAction $phaxAction
     * @param string $slugParty
     * @param string $slugGame
     * 
     * @return \Phax\CoreBundle\Model\PhaxReaction
     */
    public function moveAction(PhaxAction $phaxAction, $slugParty, $slugGame)
    {
        $partyService   = $this->get('el_core.party');          /* @var $partyService PartyService */
        
        $partyService->setPartyBySlug($slugParty, $slugGame, $phaxAction->getLocale(), $this->container);
        
        $loggedPlayer   = $this->get('el_core.session')->getPlayer();
        $coreParty      = $partyService->getParty();            /* @var $coreParty Party */
        $extendedParty  = $partyService->loadExtendedParty();   /* @var $extendedParty CheckersParty */
        $from           = new Coords($phaxAction->from['line'], $phaxAction->from['col']);
        $to             = new Coords($phaxAction->to['line'], $phaxAction->to['col']);
        
        // Check if party is still active
        if ($coreParty->getState() !== Party::ACTIVE) {
            return $this->get('phax')->reaction(array(
                'valid' => false,
                'error' => 'party has ended',
            ));
        }
        
        // Check if the move come from the good player turn
        $playerTurn = $coreParty
                ->getSlots()
                ->get($extendedParty->getCurrentPlayer() ? 1 : 0)
                ->getPlayer()
        ;
        
        if ($playerTurn->getId() !== $loggedPlayer->getId()) {
            return $this->get('phax')->reaction(array(
                'valid' => false,
                'error' => 'not your turn',
            ));
        }
        
        $checkersService = $this->get('checkers.core'); /* @var $checkersService \EL\CheckersBundle\Services\Checkers */
        
        try {
            // Perform move
            $checkersService->move($extendedParty, $from, $to, $loggedPlayer);
            
            // Update party in database
            $this->getDoctrine()->getManager()->flush();
            
            return $this->get('phax')->reaction(array(
                'valid' => true,
                'party' => $extendedParty,
            ));
        } catch (CheckersIllegalMoveException $e) {
            return $this->get('phax')->reaction(array(
                'valid' => false,
                'error' => $e->getMessage(),
            ));
        }
    }
    
    /**
     * get last move
     * 
     * @param \Phax\CoreBundle\Model\PhaxAction $phaxAction
     * 
     * @return \Phax\CoreBundle\Model\PhaxReaction
     */
    public function getLastMoveAction(PhaxAction $phaxAction, $slugParty, $slugGame)
    {
        $partyService   = $this->get('el_core.party');          /* @var $partyService PartyService */
        
        $partyService->setPartyBySlug($slugParty, $slugGame, $phaxAction->getLocale(), $this->container);
        
        $extendedParty  = $partyService->loadExtendedParty();   /* @var $extendedParty CheckersParty */
        
        return $this->get('phax')->reaction(array(
            'party' => $extendedParty->jsonSerialize(),
        ));
    }
}
