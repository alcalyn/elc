<?php

namespace EL\ELTicTacToeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EL\ELCoreBundle\Model\ELGameInterface;
use EL\ELTicTacToeBundle\Form\Type\TicTacToePartyOptionsType;
use EL\ELTicTacToeBundle\Form\Entity\TicTacToePartyOptions;


class DefaultController extends Controller implements ELGameInterface
{
    public function gameOptionsAction()
    {
        return $this->render('ELTicTacToeBundle:Default:game_options.html.twig');
    }
    
    
    
    public function getOptionsType()
    {
        return new TicTacToePartyOptionsType();
    }
}
