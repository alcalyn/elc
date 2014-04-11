<?php

namespace EL\AbstractGameBundle\Model;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\CoreBundle\Services\PartyService;
use EL\CoreBundle\Entity\Party as CoreParty;
use EL\CoreBundle\Exception\ELUserException;
use EL\AbstractGameBundle\Form\Entity\AdapterOptions;
use EL\AbstractGameBundle\Form\Type\AdapterOptionsType;

class ELGameAdapter extends Controller implements ELGameInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPartyType()
    {
        return new AdapterOptionsType();
    }
    
    /**
     * {@inheritdoc}
     */
    public function createParty()
    {
        return new AdapterOptions();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getGameLayout()
    {
        return 'CoreBundle::layout.html.twig';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCreationFormTemplate()
    {
        return 'AbstractGameBundle:Adapter:optionsForm.html.twig';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDisplayOptionsTemplate()
    {
        return 'AbstractGameBundle:Adapter:displayOptions.html.twig';
    }
    
    /**
     * {@inheritdoc}
     */
    public function saveParty(CoreParty $coreParty, $options)
    {
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function loadParty(CoreParty $coreParty)
    {
        return new AdapterOptions();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSlotsConfiguration($options)
    {
        return array(
            'parameters' => array(
                'allow_add_slots'       => true,
                'allow_remove_slots'    => true,
                'min_slots_number'      => 2,
                'max_slots_number'      => 8,
                'allow_reorder_slots'   => true,
                'allow_close_slots'     => true,
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
                array(
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
     * {@inheritdoc}
     */
    public function canStart(PartyService $partyService)
    {
        $nbPlayerMin    = $partyService->getGame()->getNbplayerMin();
        $nbPlayerMax    = $partyService->getGame()->getNbplayerMax();
        $nbPlayer       = $partyService->getNbPlayer();
        $t              = $this->get('translator');
        
        if ($nbPlayer < $nbPlayerMin) {
            throw new ELUserException($t->trans('cannot.start.notenoughplayer'), -1, ELUserException::TYPE_WARNING);
        }
        
        if ($nbPlayer > $nbPlayerMax) {
            throw new ELUserException($t->trans('cannot.start.toomanyplayer'), -1, ELUserException::TYPE_WARNING);
        }
        
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function activeAction($_locale, PartyService $partyService)
    {
        return $this->render('AbstractGameBundle:Adapter:active.html.twig', array(
            'game'          => $partyService->getGame(),
            'coreParty'     => $partyService->getParty(),
            'gameLayout'    => $this->getGameLayout(),
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function endedAction($_locale, PartyService $partyService)
    {
        return $this->render('AbstractGameBundle:Adapter:ended.html.twig', array(
            'game'          => $partyService->getGame(),
            'coreParty'     => $partyService->getParty(),
            'gameLayout'    => $this->getGameLayout(),
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCurrentDescription($_locale, PartyService $partyService)
    {
        $party = $partyService->getParty();
        
        if ($party->getState() === CoreParty::PREPARATION) {
            return 'Preparation...';
        }
        
        if ($party->getState() === CoreParty::STARTING) {
            return 'Started !';
        }
        
        if ($party->getState() === CoreParty::ACTIVE) {
            return 'Now playing';
        }
        
        if ($party->getState() === CoreParty::ENDED) {
            // should not appear...
            return 'Ended.';
        }
        
        throw new ELCoreException('Unknown party state : '.$party->getState());
    }
    
    /**
     * {@inheritdoc}
     */
    public function isMyTurn(PartyService $partyService)
    {
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function createRemake($slugParty, CoreParty $corePartyClone)
    {
        return new stdClass();
    }
}
