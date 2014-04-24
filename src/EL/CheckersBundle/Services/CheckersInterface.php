<?php

namespace EL\CheckersBundle\Services;

use EL\CoreBundle\Entity\Party;
use EL\AbstractGameBundle\Model\ELGameAdapter;
use EL\CheckersBundle\Entity\CheckersParty;
use EL\CheckersBundle\Form\Type\CheckersOptionsType;
use EL\CheckersBundle\Util\CheckersVariant;

class CheckersInterface extends ELGameAdapter
{
    public function getPartyType()
    {
        return new CheckersOptionsType($this->get('translator'));
    }
    
    public function createParty()
    {
        return new CheckersVariant();
    }
    
    public function getGameLayout()
    {
        return 'CheckersBundle::layout.html.twig';
    }
    
    public function getCreationFormTemplate()
    {
        $jsVars     = $this->get('el_core.js_vars');    /* @var $jsVars \EL\CoreBundle\Services\JsVarsService */
        $checkers   = $this->get('checkers.core');      /* @var $checkers Checkers */
        $variants   = array();
        
        $jsVars
                ->addContext('variants', $checkers->getVariants())
        ;
        
        return 'CheckersBundle:Checkers:optionsForm.html.twig';
    }
    
    /**
     * @param \EL\CoreBundle\Entity\Party $coreParty
     * @param CheckersVariant $checkersVariant
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
        $em->flush();
        
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
}
