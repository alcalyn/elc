<?php

namespace EL\Bundle\Game\AwaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phax\CoreBundle\Model\PhaxAction;
use EL\Bundle\CoreBundle\Exception\ELCoreException;
use EL\Core\Entity\Party;
use EL\Bundle\CoreBundle\Services\PartyService;
use EL\Game\Awale\Entity\AwaleParty;
use EL\Bundle\Game\AwaleBundle\Services\AwaleCore;

class AwaleController extends Controller
{
    /**
     * Refresh action
     * 
     * @param PhaxAction $phaxAction
     * @param string     $slugParty
     * 
     * @return \Phax\CoreBundle\Model\PhaxReaction
     */
    public function refreshAction(PhaxAction $phaxAction, $slugParty, $slugGame)
    {
        $awaleCore          = $this->get('awale.core');             /* @var $awaleCore    AwaleCore */
        $partyService       = $this->get('el_core.party');          /* @var $partyService PartyService */
        
        $partyService->setPartyBySlug($slugParty, $slugGame, $phaxAction->getLocale(), $this->container);
        
        $coreParty          = $partyService->getParty();            /* @var $coreParty     Party */
        $extendedParty      = $partyService->loadExtendedParty();   /* @var $extendedParty AwaleParty */
        $awaleParty         = $extendedParty->jsonSerialize();
        $awaleParty['grid'] = $awaleCore->unserializeGrid($extendedParty->getGrid());
        
        return $this->get('phax')->reaction(array(
            'coreParty'     => $coreParty,
            'awaleParty'    => $awaleParty,
        ));
    }
    
    /**
     * Play action
     * 
     * @param PhaxAction $phaxAction
     * @param string     $slugParty
     * @param integer    $box
     * 
     * @return \Phax\CoreBundle\Model\PhaxReaction
     */
    public function playAction(PhaxAction $phaxAction, $slugParty, $slugGame, $box)
    {
        $box            = intval($box);
        $t              = $this->get('translator');
        $partyService   = $this->get('el_core.party');          /* @var $partyService PartyService */
        
        $partyService->setPartyBySlug($slugParty, $slugGame, $phaxAction->getLocale(), $this->container);
        
        $coreParty      = $partyService->getParty();            /* @var $coreParty \EL\Core\Entity\Party */
        $extendedParty  = $partyService->loadExtendedParty();   /* @var $extendedParty AwaleParty */
        
        // Check value box
        if ($box < 0 || $box > 5) {
            return $this->get('phax')->error('bow must be in [0;6[, got '.$box);
        }
        
        // Check if party is still active
        if ($coreParty->getState() !== Party::ACTIVE) {
            return $this->get('phax')->error($t->trans('party.has.ended'));
        }
        
        // Check player turn
        $slots              = $coreParty->getSlots();   /* @var $slots \Doctrine\Common\Collections\Collection */
        $currentPlayerIndex = intval($extendedParty->getCurrentPlayer());
        $currentPlayer      = $slots->get($currentPlayerIndex)->getPlayer();
        $sessionPlayer      = $this->get('el_core.session')->getPlayer();
        
        if ($currentPlayer->getId() !== $sessionPlayer->getId()) {
            return $this->get('phax')->error($t->trans('not.your.turn'));
        }
        
        // Check if box is not empty
        $awaleCore  = $this->get('awale.core');             /* @var $awaleCore AwaleCore */
        $grid       = $awaleCore->unserializeGrid($extendedParty->getGrid());
        
        if (0 === $grid[$currentPlayerIndex]['seeds'][$box]) {
            return $this->get('phax')->error($t->trans('container.is.empty'));
        }
        
        // Play turn
        $newGrid = $awaleCore->play($grid, $currentPlayerIndex, $box);
        
        // Let the opponent play
        if (!$awaleCore->hasSeeds($newGrid, 1 - $currentPlayerIndex)) {
            if ($awaleCore->canFeedOpponent($grid, $currentPlayerIndex)) {
                return $this->get('phax')->error($t->trans('feed.the.opponent'));
            } else {
                $newGrid = $awaleCore->storeRemainingSeeds($newGrid);
            }
        }
        
        // Update awale party
        $extendedParty
                ->setGrid($awaleCore->serializeGrid($newGrid))
                ->setLastMove($awaleCore->getUpdatedLastMove($extendedParty->getLastMove(), $box))
                ->setCurrentPlayer(1 - $currentPlayerIndex)
        ;
        
        // Update scores
        $slot0 = $slots->get(0);    /* @var $slot0 \EL\Core\Entity\Slot */
        $slot1 = $slots->get(1);    /* @var $slot1 \EL\Core\Entity\Slot */
        
        $slot0->setScore($newGrid[0]['attic']);
        $slot1->setScore($newGrid[1]['attic']);
        
        // Check for winner
        $win = $awaleCore->hasWinner($newGrid, $extendedParty->getSeedsPerContainer());
        
        if ($win !== AwaleCore::NO_WIN) {
            $this->stopAndScoreParty($coreParty);
        }
        
        // Persist entities
        $em = $this->getDoctrine()->getManager();
        $em->persist($extendedParty);
        $em->persist($slot0);
        $em->persist($slot1);
        
        // Return updated party
        $jsonExtendedParty          = $extendedParty->jsonSerialize();
        $jsonExtendedParty['grid']  = $awaleCore->unserializeGrid($extendedParty->getGrid());
        
        return $this->get('phax')->reaction(array(
            'coreParty'     => $coreParty,
            'awaleParty'    => $jsonExtendedParty,
        ));
    }
    
    /**
     * Stop $coreParty and add elo and wld score to players
     * 
     * @param \EL\Core\Entity\Party $coreParty
     */
    private function stopAndScoreParty(Party $coreParty)
    {
        $eloService = $this->get('el_core.score.elo');  /* @var $eloService \EL\Bundle\CoreBundle\Services\EloService */
        $wldService = $this->get('el_core.score.wld');  /* @var $wldService \EL\Bundle\CoreBundle\Services\WLDService */
        
        // stop party
        $coreParty->setState(Party::ENDED);
        
        $slots      = $coreParty->getSlots();   /* @var $slots \Doctrine\Common\Collections\Collection */
        $slot0      = $slots->get(0);           /* @var $slot0 \EL\Core\Entity\Slot */
        $slot1      = $slots->get(1);           /* @var $slot1 \EL\Core\Entity\Slot */
        $score0     = $slot0->getScore();
        $score1     = $slot1->getScore();
        $player0    = $slot0->getPlayer();
        $player1    = $slot1->getPlayer();
        $game       = $coreParty->getGame();
        
        if ((null === $player0) || (null === $player1)) {
            throw new ELCoreException('Cannot update score because there is only one player in party');
        }
        
        // player 0 wins
        if ($score0 > $score1) {
            $eloService->win($player0, $player1, $game, $coreParty);
            
            $wldService->win($player0, $game, $coreParty);
            $wldService->lose($player1, $game, $coreParty);
        }
        
        // player 1 wins
        if ($score0 < $score1) {
            $eloService->lose($player0, $player1, $game, $coreParty);
            
            $wldService->lose($player0, $game, $coreParty);
            $wldService->win($player1, $game, $coreParty);
        }
        
        // draw
        if ($score0 === $score1) {
            $eloService->draw($player0, $player1, $game, $coreParty);
            
            $wldService->draw($player0, $game, $coreParty);
            $wldService->draw($player1, $game, $coreParty);
        }
    }
}
