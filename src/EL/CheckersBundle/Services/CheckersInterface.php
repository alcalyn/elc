<?php

namespace EL\CheckersBundle\Services;

use EL\CoreBundle\Entity\Party;
use EL\CoreBundle\Services\PartyService;
use EL\CoreBundle\Services\SessionService;
use EL\AbstractGameBundle\Model\ELGameAdapter;
use EL\CheckersBundle\EventListener\PartyEventListener;
use EL\CheckersBundle\Entity\CheckersParty;
use EL\CheckersBundle\Form\Type\CheckersOptionsType;
use EL\CheckersBundle\Checkers\Variant;

class CheckersInterface extends ELGameAdapter
{
    public function init()
    {
        $em = $this->getDoctrine()->getManager();
        $checkers = $this->get('checkers.core');
        $this->get('event_dispatcher')->addSubscriber(new PartyEventListener($em, $checkers));
    }
    
    public function getPartyType()
    {
        return new CheckersOptionsType($this->get('translator'));
    }
    
    public function createStandardOptions()
    {
        $variants = $this->get('checkers.variants');
        return $variants->getVariant(Variant::ENGLISH);
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
    
    /**
     * 
     * @param string $_locale
     * @param \EL\CoreBundle\Services\PartyService $partyService
     * @param CheckersParty $extendedParty
     * 
     * @return \Symfony\Component\HttpFoundation\Response
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
        
        $baseIllustrationUrl = 'bundles/checkers/img/illegalmove/';
        
        $this->get('el_core.js_vars')
                ->addContext('square-size', $squareSize)
                ->addContext('illustrations-url', $this->get('templating.helper.assets')->getUrl($baseIllustrationUrl))
                ->useTrans(array(
                    'illegal.move',
                    'not.your.turn',
                    'blow.up',
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
                    'illegalmove.cannot.huff.this.piece',
                    'illegalmove.cannot.huff.already.huffed',
                    'illegalmove.cannot.huff.already.jumped',
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
    
    /**
     * @param CheckersParty $oldParty
     * 
     * @return \EL\CheckersBundle\Checkers\Variant
     */
    public function getOptions($oldParty)
    {
        return new Variant($oldParty->getParameters());
    }
}
