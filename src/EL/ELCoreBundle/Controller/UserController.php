<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UserController extends Controller
{
    public function indexAction()
    {
        $player = $this->get('el_core.session')->getPlayer();
        
        return $this->render('ELCoreBundle:User:login-bar.html.twig', array(
            'player'    => $player,
        ));
    }
}
