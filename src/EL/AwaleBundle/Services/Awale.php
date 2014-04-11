<?php

namespace EL\AwaleBundle\Services;

use EL\CoreBundle\Entity\Party;
use EL\CoreBundle\Services\PartyService;
use EL\AbstractGameBundle\Model\ELGameAdapter;
use EL\AwaleBundle\Form\Type\AwalePartyType;
use EL\AwaleBundle\Entity\AwaleParty;

class Awale extends ELGameAdapter
{
    public function getPartyType()
    {
        return new AwalePartyType();
    }
    
    public function createParty()
    {
        return new AwaleParty();
    }
    
    public function saveParty(Party $coreParty, $awaleParty)
    {
        $em                 = $this->getDoctrine()->getManager();
        $awaleCore          = $this->get('awale.core');
        $seedsPerContainer  = $awaleParty->getSeedsPerContainer();
        
        $awaleParty
                ->setParty($coreParty)
                ->setGrid($awaleCore->fillGrid($seedsPerContainer))
        ;
        
        $em->persist($awaleParty);
        $em->flush();
        
        return true;
    }
    
    public function loadParty(Party $coreParty)
    {
        $em = $this->getDoctrine()->getManager();
        
        $party = $em
                ->getRepository('AwaleBundle:AwaleParty')
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
                'allow_reorder_slots'   => true,
                'allow_close_slots'     => false,
                'allow_invite_cpu'      => false,
            ),
            'slots' => array(
                array(
                    'open'      => true,
                    'host'      => true,
                    'score'     => 0,
                ),
                array(
                    'open'      => true,
                    'score'     => 0,
                ),
            ),
        );
    }
    
    public function getDisplayOptionsTemplate()
    {
        return 'AwaleBundle:Awale:displayOptions.html.twig';
    }
    
    public function getGameLayout()
    {
        return 'AwaleBundle::layout.html.twig';
    }
    
    public function activeAction($_locale, PartyService $partyService)
    {
        return $this->render('AwaleBundle:Awale:active.html.twig', array(
            'game'          => $partyService->getGame(),
            'coreParty'     => $partyService->getParty(),
            'gameLayout'    => $this->getGameLayout(),
        ));
    }
}
