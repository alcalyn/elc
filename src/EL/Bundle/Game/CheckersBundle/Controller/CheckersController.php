<?php

namespace EL\Bundle\Game\CheckersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phax\CoreBundle\Model\PhaxAction;
use EL\Bundle\CoreBundle\Entity\Party;
use EL\Bundle\CoreBundle\Util\Coords;
use EL\Bundle\CoreBundle\Services\PartyService;
use EL\Bundle\Game\CheckersBundle\Entity\CheckersParty;
use EL\Bundle\Game\CheckersBundle\Checkers\CheckersIllegalMoveException;
use EL\Bundle\Game\CheckersBundle\Checkers\Variant;
use EL\Bundle\Game\CheckersBundle\Services\Checkers;

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
        $t              = $this->get('translator');
        
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
        
        $checkersService    = $this->get('checkers.core');
        $variantName        = $checkersService->getVariantName(new Variant($extendedParty->getParameters()));
        
        try {
            // Perform move
            $checkersService->move($extendedParty, $from, $to, $loggedPlayer);
            
            // Check if party has ended
            $winner = $checkersService->checkPartyEnd($extendedParty);
            
            if (null !== $winner) {
                $this->endParty($extendedParty, $winner, $variantName);
            }
            
            // Send success response
            return $this->get('phax')->reaction(array(
                'valid' => true,
                'party' => $extendedParty,
            ));
        } catch (CheckersIllegalMoveException $e) {
            // Send error response
            return $this->get('phax')->reaction(array(
                'valid' => false,
                'error' => $t->trans($e->getMessage(), $e->getMsgVars()),
                'illus' => $e->getIllustration(),
            ));
        }
    }
    
    /**
     * A player tries to huff a piece
     * 
     * @param \Phax\CoreBundle\Model\PhaxAction $phaxAction
     * @param string $slugParty
     * @param string $slugGame
     */
    public function huffAction(PhaxAction $phaxAction, $slugParty, $slugGame)
    {
        $partyService   = $this->get('el_core.party');          /* @var $partyService PartyService */
        
        $partyService->setPartyBySlug($slugParty, $slugGame, $phaxAction->getLocale(), $this->container);
        
        $loggedPlayer   = $this->get('el_core.session')->getPlayer();
        $coreParty      = $partyService->getParty();            /* @var $coreParty Party */
        $extendedParty  = $partyService->loadExtendedParty();   /* @var $extendedParty CheckersParty */
        $t              = $this->get('translator');
        
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
        
        $checkersService    = $this->get('checkers.core');
        $coords             = new Coords($phaxAction->coords['line'], $phaxAction->coords['col']);
        
        try {
            // Perform huff
            $checkersService->huff($extendedParty, $coords);
            
            // Send success response
            return $this->get('phax')->reaction(array(
                'valid' => true,
                'party' => $extendedParty,
            ));
        } catch (CheckersIllegalMoveException $e) {
            
            // Send error response
            return $this->get('phax')->reaction(array(
                'valid' => false,
                'error' => $t->trans($e->getMessage(), $e->getMsgVars()),
                'illus' => $e->getIllustration(),
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
    
    /**
     * End party
     * 
     * @param \EL\Bundle\Game\CheckersBundle\Entity\CheckersParty $checkersParty
     * @param mixed $winner Checkers WHITE, BLACK or DRAW
     * @param string $variantName
     */
    private function endParty(CheckersParty $checkersParty, $winner, $variantName)
    {
        $coreParty = $checkersParty->getParty();
        
        $coreParty->setState(Party::ENDED);
        $slots = $coreParty->getSlots();
        
        $eloService = $this->get('el_core.score.elo'); /* @var $eloService \EL\Bundle\CoreBundle\Services\EloService */
        $wldService = $this->get('el_core.score.wld'); /* @var $wldService \EL\Bundle\CoreBundle\Services\WLDService */
        
        $game = $checkersParty->getParty()->getGame();
        $gameVariant = $eloService->getGameVariant($game, $variantName);
        
        if (Checkers::DRAW !== $winner) {
            $slots->get($winner ? 1 : 0)->addScore(1);
        
            $pWinner = $slots->get($winner ? 1 : 0)->getPlayer();
            $pLooser = $slots->get($winner ? 0 : 1)->getPlayer();

            // Update scores in default_variant
            $eloService->win($pWinner, $pLooser, $game, $coreParty);
            $wldService->win($pWinner, $game, $coreParty);
            $wldService->lose($pLooser, $game, $coreParty);

            // Update score in checkers variant
            $eloService->win($pWinner, $pLooser, $gameVariant, $coreParty);
            $wldService->win($pWinner, $gameVariant, $coreParty);
            $wldService->lose($pLooser, $gameVariant, $coreParty);
        } else {
            $p0 = $slots->get(0)->getPlayer();
            $p1 = $slots->get(1)->getPlayer();
            
            // Update scores in default_variant
            $eloService->draw($p0, $p1, $game, $coreParty);
            $wldService->draw($p0, $game, $coreParty);
            $wldService->draw($p1, $game, $coreParty);

            // Update score in checkers variant
            $eloService->draw($p0, $p1, $gameVariant, $coreParty);
            $wldService->draw($p0, $gameVariant, $coreParty);
            $wldService->draw($p1, $gameVariant, $coreParty);
        }
    }
}
