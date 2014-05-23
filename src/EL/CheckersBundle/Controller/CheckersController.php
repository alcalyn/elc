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

class CheckersController extends Controller
{
    public function moveAction(PhaxAction $phaxAction, $slugParty, $slugGame)
    {
        $partyService   = $this->get('el_core.party');          /* @var $partyService PartyService */
        
        $partyService->setPartyBySlug($slugParty, $slugGame, $phaxAction->getLocale(), $this->container);
        
        $coreParty      = $partyService->getParty();            /* @var $coreParty Party */
        $extendedParty  = $partyService->loadExtendedParty();   /* @var $extendedParty CheckersParty */
        $from           = new Coords($phaxAction->from['line'], $phaxAction->from['col']);
        $to             = new Coords($phaxAction->to['line'], $phaxAction->to['col']);
        $position       = $partyService->position();
        
        // Check if party is still active
        if ($coreParty->getState() !== Party::ACTIVE) {
            return $this->get('phax')->error('party.has.ended');
        }
        
        $checkersService = $this->get('checkers.core'); /* @var $checkersService \EL\CheckersBundle\Services\Checkers */
        
        try {
            // Perform move
            $checkersService->move($extendedParty, $position, $from, $to);
            
            // Update party in database
            $this->getDoctrine()->getManager()->flush();
            
        } catch (CheckersIllegalMoveException $e) {
            return $this->get('phax')->error($e->getMessage());
        }
    }
}
