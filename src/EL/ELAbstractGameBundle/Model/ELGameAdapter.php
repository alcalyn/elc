<?php

namespace EL\ELAbstractGameBundle\Model;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\ELCoreBundle\Entity\Party as CoreParty;
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
    public function loadParty($_locale, $slug_party)
    {
    	return new stdClass();
    }
    
    /**
     * {@inheritdoc}
     */
    public function activeAction($_locale, $party_service)
    {
    	return $this->render('ELAbstractGameBundle:Adapter:active.html.twig', array(
    		'game'		=> $party_service->getGame(),
    		'party'		=> $party_service->getParty(),
    	));
    }
    
    /**
     * {@inheritdoc}
     */
    public function endedAction($_locale, $party_service)
    {
    	return $this->render('ELAbstractGameBundle:Adapter:ended.html.twig', array(
    		'game'		=> $party_service->getGame(),
    		'party'		=> $party_service->getParty(),
    	));
    }
    
    /**
     * {@inheritdoc}
     */
    public function createClone($slug_party, $core_party_clone)
    {
    	return new stdClass();
    }
    
    
}
