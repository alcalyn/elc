<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class WidgetController extends Controller
{
    
    public function myPartiesAction($_locale)
    {
        $em = $this->getDoctrine()->getManager();
        
        $parties = $em
                ->getRepository('ELCoreBundle:Party')
                ->findCurrentPartiesForPlayer($_locale, $this->getUser())
        ;
        
        $games_list = array();
        
        foreach ($parties as $party) {
            $game = $party->getGame();
            $games_list[$game->getId()] = $game->getTitle();
        }
        
        return $this->render('ELCoreBundle:Widget:my-parties.html.twig', array(
            'parties'       => $parties,
            'games_list'    => $games_list,
        ));
    }
}
