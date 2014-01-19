<?php

namespace EL\ELTicTacToeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\ELAbstractGameBundle\Model\ELGameAdapter;
use EL\ELTicTacToeBundle\Form\Type\TicTacToePartyOptionsType;
use EL\ELTicTacToeBundle\Form\Entity\TicTacToePartyOptions;
use EL\ELTicTacToeBundle\Entity\Party;
use EL\ELCoreBundle\Entity\Party as CoreParty;


class DefaultController extends ELGameAdapter
{
    public function gameOptionsAction()
    {
        return $this->render('ELTicTacToeBundle:Default:game_options.html.twig');
    }
    
    
    
    public function getOptionsType()
    {
        return new TicTacToePartyOptionsType();
    }
    
    public function getOptions()
    {
        return new TicTacToePartyOptions();
    }
    
    public function saveOptions(CoreParty $core_party, $options)
    {
    	$em = $this->getDoctrine()->getManager();
    	
        $party = new Party();
        
        $current_player = $options->getFirstPlayer();
        
        if ($current_player == 0) {
        	$current_player = rand(1, 2);
        }
        
        $party
                ->setParty($core_party)
                ->setFirstPlayer($options->getFirstPlayer())
                ->setCurrentPlayer($current_player)
        ;
        
        $em->persist($party);
        $em->flush();
        
        return true;
    }
    
    public function loadOptions(CoreParty $core_party)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$party = $em
    			->getRepository('ELTicTacToeBundle:Party')
    			->findOneBySlugParty($core_party->getSlug())
    	;
    	
    	$options = new TicTacToePartyOptions();
    	
    	$options->setFirstPlayer($party->getFirstPlayer());
    	
    	return $options;
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
            ),
        );
    }
    
	public function loadParty($_locale, $slug_party)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$party = $em
    			->getRepository('ELTicTacToeBundle:Party')
    			->findOneBySlugParty($slug_party);
    	
    	return $party;
    }
    
	public function activeAction($_locale, $party_service)
	{
		$em = $this->getDoctrine()->getManager();
		
		$game		= $party_service->getGame();
		$core_party	= $party_service->getParty();

        $party = $em
                ->getRepository('ELTicTacToeBundle:Party')
                ->findOneByCoreParty($core_party)
        ;
		
    	return $this->render('ELTicTacToeBundle:Default:active.html.twig', array(
    		'game'			=> $game,
    		'core_party'	=> $core_party,
    		'party'			=> $party,
    	));
    }
    
	public function createClone($slug_party, $clone_core_party)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$extended_party = $em
                ->getRepository('ELTicTacToeBundle:Party')
                ->findOneBySlugParty($slug_party)
        ;
        
        $clone_extended_party = $extended_party->createClone($clone_core_party);
        
        $em->persist($clone_extended_party);
        $em->flush();
    }
    
}
