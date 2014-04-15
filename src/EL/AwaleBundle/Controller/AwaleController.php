<?php

namespace EL\AwaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phax\CoreBundle\Model\PhaxAction;
use EL\CoreBundle\Exception\ELCoreException;
use EL\CoreBundle\Exception\ELUserException;
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
    public function refreshAction(PhaxAction $phaxAction, $slugParty)
    {
        $awaleCore      = $this->get('awale.core');             /* @var $awaleCore AwaleCore */
        $partyService   = $this->get('el_core.party');          /* @var $partyService PartyService */
        
        $partyService->setPartyBySlug($slugParty, 'awale', $phaxAction->getLocale(), $this->container);
        
        $extendedParty  = $partyService->loadExtendedParty();   /* @var $extendedParty AwaleParty */
        $grid           = $awaleCore->unserializeGrid($extendedParty->getGrid());
        
        return $this->get('phax')->reaction(array(
            'grid' => $grid,
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
    public function playAction(PhaxAction $phaxAction, $slugParty, $box)
    {
        $partyService   = $this->get('el_core.party');          /* @var $partyService PartyService */
        
        $partyService->setPartyBySlug($slugParty, 'awale', $phaxAction->getLocale(), $this->container);
        
        $coreParty      = $partyService->getParty();            /* @var $coreParty \EL\CoreBundle\Entity\Party */
        $extendedParty  = $partyService->loadExtendedParty();   /* @var $extendedParty AwaleParty */
        
        // Check value box
        
        if ($box < 0 || $box > 5) {
            throw new ELCoreException('bow must be in [0;6[, got '.$box);
        }
        
        // Check player turn
        $currentPlayerIndex = $extendedParty->getCurrentPlayer();
        $currentPlayer      = $coreParty->getSlots()->get($currentPlayerIndex)->getPlayer();
        $sessionPlayer      = $this->get('el_core.session')->getPlayer();
        
        if ($currentPlayer->getId() !== $sessionPlayer->getId()) {
            throw new ELUserException('not.your.turn');
        }
        
        $awaleCore      = $this->get('awale.core');             /* @var $awaleCore AwaleCore */
        $grid           = $awaleCore->unserializeGrid($extendedParty->getGrid());
        
        // Check if box is not empty
        if (0 === $grid[$currentPlayerIndex]['seeds'][$box]) {
            throw new ELUserException('this.container.is.empty');
        }
        
        // Update awale party
        $em         = $this->getDoctrine()->getManager();
        $newGrid    = $awaleCore->play($grid, $currentPlayerIndex, $box);
        
        $extendedParty
                ->setGrid($awaleCore->serializeGrid($newGrid))
                ->setLastMove($awaleCore->getUpdatedLastMove($extendedParty, $box))
                ->setCurrentPlayer(1 - $extendedParty->getCurrentPlayer())
        ;
        
        $em->persist($extendedParty);
        $em->flush();
        
        return $this->get('phax')->reaction(array(
            'awaleParty' => $extendedParty,
        ));
    }
}
