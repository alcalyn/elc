<?php

namespace EL\CheckersBundle\Services;

use EL\CoreBundle\Entity\Party;
use EL\CoreBundle\Services\PartyService;
use EL\CoreBundle\Services\SessionService;
use EL\AbstractGameBundle\Model\ELGameAdapter;
use EL\CheckersBundle\Entity\CheckersParty;
use EL\CheckersBundle\Form\Type\CheckersOptionsType;
use EL\CheckersBundle\Checkers\Variant;
use EL\CheckersBundle\Checkers\Move;

class CheckersInterface extends ELGameAdapter
{
    public function getPartyType()
    {
        return new CheckersOptionsType($this->get('translator'));
    }
    
    public function createParty()
    {
        return new Variant();
    }
    
    public function getGameLayout()
    {
        return 'CheckersBundle::layout.html.twig';
    }
    
    public function getCreationFormTemplate()
    {
        $jsVars     = $this->get('el_core.js_vars');    /* @var $jsVars \EL\CoreBundle\Services\JsVarsService */
        $checkers   = $this->get('checkers.core');      /* @var $checkers Checkers */
        
        $jsVars
                ->addContext('variants', $checkers->getVariants())
        ;
        
        return 'CheckersBundle:Checkers:optionsForm.html.twig';
    }
    
    /**
     * @param Party $coreParty
     * @param CheckersParty $extendedParty
     * 
     * @return array
     */
    public function getDisplayOptionsTemplate(Party $coreParty, $extendedParty)
    {
        $checkers       = $this->get('checkers.core');                              /* @var $checkers Checkers */
        $variant        = new Variant($extendedParty->getParameters());
        $variantName    = $checkers->getVariantName($variant);
        
        return array(
            'template'  => 'CheckersBundle:Checkers:displayOptions.html.twig',
            'vars'      => array(
                'variant'       => $variant,
                'variantName'   => $variantName,
            ),
        );
    }
    
    /**
     * @param \EL\CoreBundle\Entity\Party $coreParty
     * @param Variant $checkersVariant
     * 
     * @return boolean
     */
    public function saveParty(Party $coreParty, $checkersVariant)
    {
        $checkersParty = new CheckersParty();
        $checkersParty
                ->setParty($coreParty)
                ->setCurrentPlayer($checkersVariant->getFirstPlayer())
                ->setParameters($checkersVariant->getBinaryValue())
        ;
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($checkersParty);
        
        return true;
    }
    
    public function loadParty(Party $coreParty)
    {
        $em = $this->getDoctrine()->getManager();
        
        $party = $em
                ->getRepository('CheckersBundle:CheckersParty')
                ->findOneBy(array(
                    'party' => $coreParty,
                ))
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
                'allow_reorder_slots'   => false,
                'allow_close_slots'     => false,
                'allow_invite_cpu'      => false,
            ),
            'slots' => array(
                array(
                    'host'      => true,
                    'open'      => true,
                    'score'     => 0,
                ),
                array(
                    'open'      => true,
                    'score'     => 0,
                ),
            ),
        );
    }
    
    public function started(PartyService $partyService)
    {
        $em             = $this->getDoctrine()->getManager();
        $checkers       = $this->get('checkers.core');          /* @var $checkers Checkers */
        $checkersParty  = $partyService->loadExtendedParty();   /* @var $checkersParty CheckersParty */
        $variant        = new Variant($checkersParty->getParameters());
        $grid           = $checkers->initGrid($variant);
        $move           = new Move(0);
        
        $checkersParty
                ->setGrid($grid)
                ->setCurrentPlayer($variant->getFirstPlayer())
                ->setLastMove(json_encode($move))
        ;
        
        $em->persist($checkersParty);
    }
    
    /**
     * 
     * @param string $_locale
     * @param \EL\CoreBundle\Services\PartyService $partyService
     * @param CheckersParty $extendedParty
     * 
     * @return type
     */
    public function activeAction($_locale, PartyService $partyService, $extendedParty)
    {
        $sessionService = $this->get('el_core.session'); /* @var $sessionService SessionService */
        $variant = new Variant($extendedParty->getParameters());
        $squareSize = 64;
        $gridSize = '';
        
        if ($variant->getBoardSize() > 10) {
            $squareSize = 48;
            $gridSize = 'grid-small';
        } elseif ($variant->getBoardSize() < 8) {
            $squareSize = 96;
            $gridSize = 'grid-large';
        }
        
        $this->get('el_core.js_vars')
                ->addContext('square-size', $squareSize)
                ->useTrans(array(
                    'illegalmove',
                    'not.your.turn',
                    'illegalmove.no.move.detected',
                    'illegalmove.destination.occupied',
                    'illegalmove.must.move.diagonally',
                    'illegalmove.cannot.move.too.far',
                    'illegalmove.cannot.backward.jump',
                    'illegalmove.cannot.move.back',
                    'illegalmove.cannot.jump.own.pieces',
                    'illegalmove.cannot.jump.two.pieces',
                    'illegalmove.king.must.stop.behind',
                    'illegalmove.no.long.range.king',
                ))
        ;
        
        return $this->render('CheckersBundle:Checkers:active.html.twig', array(
            'reverse'           => 1 === $partyService->position(),
            'gameLayout'        => $this->getGameLayout(),
            'game'              => $partyService->getGame(),
            'coreParty'         => $coreParty = $partyService->getParty(),
            'cherchersParty'    => $extendedParty,
            'variant'           => $variant,
            'slots'             => $coreParty->getSlots(),
            'player'            => $sessionService->getPlayer(),
            'grid'              => $extendedParty->getGrid(),
            'gridSize'          => $gridSize,
            'squareSize'        => $squareSize,
        ));
    }
}
