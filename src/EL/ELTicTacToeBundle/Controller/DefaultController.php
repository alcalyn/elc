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
    
    public function saveOptions(CoreParty $core_party, $options, $em)
    {
        $party = new Party();
        
        $party
                ->setParty($core_party)
                ->setFirstPlayer($options->getFirstPlayer());
        
        $em->persist($party);
        $em->flush();
        
        return true;
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
    
}
