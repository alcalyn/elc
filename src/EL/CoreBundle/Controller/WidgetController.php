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
            $gameInterface               = $partyService->getGameInterface();
            $partyDescription           = $gameInterface->getCurrentDescription($_locale, $partyService);
            $myTurn                     = $gameInterface->isMyTurn($partyService);

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
        $game               = $partyService->getGame();
        $players            = $partyService->getPlayers();
        $gameInterface       = $partyService->getGameInterface();
        $extendedOptions    = $gameInterface->loadParty($party);
        $options            = $gameInterface->getDisplayOptionsTemplate($party, $extendedOptions);
        $optionsTemplate    = is_array($options) ? $options['template'] : $options ;
        $optionsVars        = is_array($options) ? $options['vars'] : array() ;
        
        $scoreService->badgePlayers($players, $game);
        
        return $this->get('phax')->render('CoreBundle:Widget/CurrentParty:current-party.html.twig', array(
            'locale'                    => $_locale,
            'coreParty'                 => $party,
            'game'                      => $game,
            'players'                   => $players,
            'extendedOptions'           => $extendedOptions,
            'extendedOptionsTemplate'   => $optionsTemplate,
        ) + $optionsVars);
    }
}
