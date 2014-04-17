<?php

namespace EL\AwaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phax\CoreBundle\Model\PhaxAction;
use EL\CoreBundle\Exception\ELCoreException;
use EL\CoreBundle\Exception\ELUserException;
use EL\CoreBundle\Entity\Party;
use EL\CoreBundle\Services\PartyService;
use EL\AwaleBundle\Entity\AwaleParty;
use EL\AwaleBundle\Services\AwaleCore;

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
        $partyService   = $this->get('el_core.party');          /* @var $partyService PartyService */
        
        $partyService->setPartyBySlug($slugParty, $slugGame, $phaxAction->getLocale(), $this->container);
        
        $coreParty      = $partyService->getParty();            /* @var $coreParty \EL\CoreBundle\Entity\Party */
        $extendedParty  = $partyService->loadExtendedParty();   /* @var $extendedParty AwaleParty */
        
        // Check value box
        if ($box < 0 || $box > 5) {
            return $this->get('phax')->error('bow must be in [0;6[, got '.$box);
        }
        
        // Check player turn
        $currentPlayerIndex = $extendedParty->getCurrentPlayer();
        $currentPlayer      = $coreParty->getSlots()->get($currentPlayerIndex)->getPlayer();
        $sessionPlayer      = $this->get('el_core.session')->getPlayer();
        
        if ($currentPlayer->getId() !== $sessionPlayer->getId()) {
            return $this->get('phax')->error('not.your.turn');
        }
        
        // Check if box is not empty
        $awaleCore  = $this->get('awale.core');             /* @var $awaleCore AwaleCore */
        $grid       = $awaleCore->unserializeGrid($extendedParty->getGrid());
        
        if (0 === $grid[$currentPlayerIndex]['seeds'][$box]) {
            return $this->get('phax')->error('this.container.is.empty');
        }
        
        // Update awale party
        $em         = $this->getDoctrine()->getManager();
        $newGrid    = $awaleCore->play($grid, intval($currentPlayerIndex), intval($box));
        
        $extendedParty
                ->setGrid($awaleCore->serializeGrid($newGrid))
                ->setLastMove($awaleCore->getUpdatedLastMove($extendedParty->getLastMove(), $box))
                ->setCurrentPlayer(1 - $extendedParty->getCurrentPlayer())
        ;
        
        // Update scores
        $slot0 = $coreParty->getSlots()->get(0);    /* @var $slot0 \EL\CoreBundle\Entity\Slot */
        $slot1 = $coreParty->getSlots()->get(1);    /* @var $slot1 \EL\CoreBundle\Entity\Slot */
        
        $slot0->setScore($newGrid[0]['attic']);
        $slot1->setScore($newGrid[1]['attic']);
        
        // Check for winner
        $win = $awaleCore->hasWinner($newGrid, $extendedParty->getSeedsPerContainer());
        
        if ($win !== AwaleCore::NO_WIN) {
            $this->stopAndScoreParty($coreParty);
        }
        
        // Persist entities
        $em->persist($extendedParty);
        $em->persist($slot0);
        $em->persist($slot1);
        $em->flush();
        
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
     * @param \EL\CoreBundle\Entity\Party $coreParty
     */
    private function stopAndScoreParty(Party $coreParty)
    {
        $eloService = $this->get('el_core.score.elo');  /* @var $eloService \EL\CoreBundle\Services\EloService */
        $wldService = $this->get('el_core.score.wld');  /* @var $wldService \EL\CoreBundle\Services\WLDService */
        
        // stop party
        $coreParty->setState(Party::ENDED);
        
        $slot0      = $coreParty->getSlots()->get(0);    /* @var $slot0 \EL\CoreBundle\Entity\Slot */
        $slot1      = $coreParty->getSlots()->get(1);    /* @var $slot1 \EL\CoreBundle\Entity\Slot */
        $score0     = $slot0->getScore();
        $score1     = $slot1->getScore();
        $player0    = $slot0->getPlayer();
        $player1    = $slot1->getPlayer();
        $game       = $coreParty->getGame();
        
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
