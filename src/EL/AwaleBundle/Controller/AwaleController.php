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
    public function refreshAction(PhaxAction $phaxAction, $slugParty)
    {
        $awaleCore          = $this->get('awale.core');             /* @var $awaleCore    AwaleCore */
        $partyService       = $this->get('el_core.party');          /* @var $partyService PartyService */
        
        $partyService->setPartyBySlug($slugParty, 'awale', $phaxAction->getLocale(), $this->container);
        
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
    public function playAction(PhaxAction $phaxAction, $slugParty, $box)
    {
        $partyService   = $this->get('el_core.party');          /* @var $partyService PartyService */
        
        $partyService->setPartyBySlug($slugParty, 'awale', $phaxAction->getLocale(), $this->container);
        
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
        $awaleCore      = $this->get('awale.core');             /* @var $awaleCore AwaleCore */
        $grid           = $awaleCore->unserializeGrid($extendedParty->getGrid());
        
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
}
