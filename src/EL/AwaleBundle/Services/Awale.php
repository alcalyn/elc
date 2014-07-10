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
    
    public function getDisplayOptionsTemplate(Party $coreParty, $extendedParty)
    {
        return 'AwaleBundle:Awale:displayOptions.html.twig';
    }
    
    public function getGameLayout()
    {
        return 'AwaleBundle::layout.html.twig';
    }
    
    public function activeAction($_locale, PartyService $partyService, $extendedParty)
    {
        $awaleCore      = $this->get('awale.core');             /* @var $awaleCore     AwaleCore  */
        $coreParty      = $partyService->getParty();            /* @var $coreParty     Party      */
        $sessionPlayer  = $this->get('el_core.session')->getPlayer();
        $slot1Player    = $coreParty->getSlots()->get(1)->getPlayer();
        $reverse        = $sessionPlayer->getId() === $slot1Player->getId();
        
        $this->get('el_core.js_vars')
                ->useTrans('not.your.turn')
                ->useTrans('container.is.empty')
                ->useTrans('seeds.per.container')
                ->useTrans('feed.the.opponent')
        ;
        
        return $this->render('AwaleBundle:Awale:active.html.twig', array(
            'game'          => $partyService->getGame(),
            'coreParty'     => $coreParty,
            'gameLayout'    => $this->getGameLayout(),
            'reverse'       => $reverse,
            'grid'          => $awaleCore->unserializeGrid($extendedParty->getGrid()),
        ));
    }
    
    public function createRemake(PartyService $partyService, Party $corePartyClone)
    {
        $awaleCore      = $this->get('awale.core');             /* @var $awaleCore     AwaleCore */
        $oldAwaleParty  = $partyService->loadExtendedParty();   /* @var $oldAwaleParty AwaleParty */
        
        $remakeAwaleParty = new AwaleParty();
        $remakeAwaleParty
                ->setParty($corePartyClone)
                ->setSeedsPerContainer($oldAwaleParty->getSeedsPerContainer())
                ->setGrid($awaleCore->fillGrid($oldAwaleParty->getSeedsPerContainer()))
        ;
        
        return $remakeAwaleParty;
    }
}
