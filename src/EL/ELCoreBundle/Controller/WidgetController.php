<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WidgetController extends Controller
{
    public function myPartiesAction($_locale)
    {
        $em             = $this->getDoctrine()->getManager();
        $parties_list   = array();
        $games_list     = array();
        $party_service  = $this->get('el_core.party');
        
        $parties = $em
                ->getRepository('ELCoreBundle:Party')
                ->findCurrentPartiesForPlayer($_locale, $this->getUser())
        ;

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
                'link'          => $this->generateUrl('elcore_party', array(
                    'slug_game'     => $game->getSlug(),
                    'slug_party'    => $party->getSlug(),
                )),
            );
        }

        return $this->get('phax')->render('ELCoreBundle:Widget/MyParties:my-parties.html.twig', array(
            'current_parties'   => $parties_list,
            'games_list'        => $games_list,
        ));
    }
}
