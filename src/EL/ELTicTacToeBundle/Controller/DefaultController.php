<?php

namespace EL\ELTicTacToeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function gameOptionsAction()
    {
        return $this->render('ELTicTacToeBundle:Default:game_options.html.twig');
    }
}
