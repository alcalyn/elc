<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WidgetController extends Controller
{
    public function myPartiesAction($_locale)
    {
        $em             = $this->getDoctrine()->getManager();
        $partiesList    = array();
        $gamesList      = array();
        $partyService   = $this->get('el_core.party');
        
        $parties = $em
                ->getRepository('ELCoreBundle:Party')
                ->findCurrentPartiesForPlayer($_locale, $this->get('el_core.session')->getPlayer())
        ;

        foreach ($parties as $party) {
            $partyService->setParty($party, $this->container);

            $game                       = $party->getGame();
            $gamesList[$game->getId()]  = $game->getTitle();
            $extendedGame               = $partyService->getExtendedGame();
            $partyDescription           = $extendedGame->getCurrentDescription($_locale, $partyService);
            $myTurn                     = $extendedGame->isMyTurn($partyService);

            $partiesList []= array(
                'game'          => $game,
                'party'         => $party,
                'description'   => $partyDescription,
                'myTurn'       => $myTurn,
                'link'          => $this->generateUrl('elcore_party', array(
                    'slugGame'     => $game->getSlug(),
                    'slugParty'    => $party->getSlug(),
                )),
            );
        }

        return $this->get('phax')->render('ELCoreBundle:Widget/MyParties:my-parties.html.twig', array(
            'currentParties'   => $partiesList,
            'gamesList'        => $gamesList,
        ));
    }
}
