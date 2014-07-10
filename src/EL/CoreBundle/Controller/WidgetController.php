<?php

namespace EL\CoreBundle\Controller;

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
                ->getRepository('CoreBundle:Party')
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

        return $this->get('phax')->render('CoreBundle:Widget/MyParties:my-parties.html.twig', array(
            'currentParties'   => $partiesList,
            'gamesList'        => $gamesList,
        ));
    }
    
    /**
     * Controller for widget current parties
     * 
     * @param string $_locale
     * 
     * @return \Phax\CoreBundle\Model\PhaxReaction
     */
    public function currentPartyAction($_locale)
    {
        $partyService       = $this->get('el_core.party');
        $scoreService       = $this->get('el_core.score');
        $party              = $partyService->getParty();
        $players            = $partyService->getPlayers();
        $extendedGame       = $partyService->getExtendedGame();
        $extendedOptions    = $extendedGame->loadParty($party);
        
        return $this->get('phax')->render('CoreBundle:Widget/CurrentParty:current-party.html.twig', array(
            'locale'                    => $_locale,
            'coreParty'                 => $party,
            'players'                   => $players,
            'extendedOptions'           => $extendedOptions,
            'extendedOptionsTemplate'   => $extendedGame->getDisplayOptionsTemplate(),
        ));
    }
}
