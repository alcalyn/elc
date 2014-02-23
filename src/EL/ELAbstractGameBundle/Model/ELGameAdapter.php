<?php

namespace EL\ELAbstractGameBundle\Model;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\ELCoreBundle\Services\PartyService;
use EL\ELCoreBundle\Entity\Party as CoreParty;
use EL\ELCoreBundle\Model\ELUserException;
use EL\ELAbstractGameBundle\Form\Entity\AdapterOptions;
use EL\ELAbstractGameBundle\Form\Type\AdapterOptionsType;

class ELGameAdapter extends Controller implements ELGameInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOptionsType()
    {
        return new AdapterOptionsType();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        // can we delete this and return a new stdClass ?
        return new AdapterOptions();
    }
    
    /**
     * {@inheritdoc}
     */
    public function saveOptions(CoreParty $core_party, $options)
    {
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function loadOptions(CoreParty $core_party)
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
    public function loadParty($slug_party)
    {
        return new stdClass();
    }
    
    /**
     * {@inheritdoc}
     */
    public function canStart(PartyService $party_service)
    {
        $nb_player_min  = $party_service->getGame()->getNbplayerMin();
        $nb_player_max  = $party_service->getGame()->getNbplayerMax();
        $nb_player      = $party_service->getNbPlayer();
        
        if ($nb_player < $nb_player_min) {
            throw new ELUserException('cannot.start.notenoughplayer', ELUserException::TYPE_WARNING);
        }
        
        if ($nb_player > $nb_player_max) {
            throw new ELUserException('cannot.start.toomanyplayer', ELUserException::TYPE_WARNING);
        }
        
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function activeAction($_locale, PartyService $party_service)
    {
        return $this->render('ELAbstractGameBundle:Adapter:active.html.twig', array(
            'game'          => $party_service->getGame(),
            'core_party'    => $party_service->getParty(),
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function endedAction($_locale, PartyService $party_service)
    {
        return $this->render('ELAbstractGameBundle:Adapter:ended.html.twig', array(
            'game'          => $party_service->getGame(),
            'core_party'    => $party_service->getParty(),
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCurrentDescription($_locale, PartyService $party_service)
    {
        $party = $party_service->getParty();
        
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
    public function isMyTurn(PartyService $party_service)
    {
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function createRemake($slug_party, CoreParty $core_party_clone)
    {
        return new stdClass();
    }
}
