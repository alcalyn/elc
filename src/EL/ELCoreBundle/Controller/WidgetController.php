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
        
        $parties_list = array();
        $games_list = array();
        $party_service = $this->get('el_core.party');
        
        foreach ($parties as $party) {
            $party_service->setParty($party, $this->container);
            
            $game                       = $party->getGame();
            $games_list[$game->getId()] = $game->getTitle();
            $extended_game              = $party_service->getExtendedGame();
            $party_description          = $extended_game->getCurrentDescription($_locale, $party_service);
            $my_turn                    = $extended_game->isMyTurn($party_service);
            
            $parties_list []= array(
                'game'          => $game,
                'party'         => $party,
                'description'   => $party_description,
                'my_turn'       => $my_turn,
            );
        }
        
        return $this->render('ELCoreBundle:Widget:my-parties.html.twig', array(
            'current_parties'   => $parties_list,
            'games_list'        => $games_list,
        ));
    }
}
