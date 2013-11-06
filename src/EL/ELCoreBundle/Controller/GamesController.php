<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class GamesController extends Controller
{
    /**
     * @Route(
     *      "/en/games",
     *      defaults = {"_locale": "en"},
     *      name = "elcore_en_games_list"
     * )
     * @Route(
     *      "/fr/jeux",
     *      defaults = {"_locale": "fr"},
     *      name = "elcore_fr_games_list"
     * )
     */
    public function listAction($_locale)
    {
        $em = $this->getDoctrine()->getManager();
        
        $games = $em
                ->getRepository('ELCoreBundle:Game')
                ->findAllByLang($_locale);
        
        return $this->render('ELCoreBundle:Games:list.html.twig', array(
            'games' => $games,
        ));
    }
    
    
    /**
     * @Route(
     *      "/en/games/{slug}",
     *      defaults = {"_locale": "en"},
     *      name = "elcore_en_game_home"
     * )
     * @Route(
     *      "/fr/jeux/{slug}",
     *      defaults = {"_locale": "fr"},
     *      name = "elcore_fr_game_home"
     * )
     */
    public function homeAction($_locale, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $game = $em
                ->getRepository('ELCoreBundle:Game')
                ->findByLang($_locale, $slug);
        
        return $this->render('ELCoreBundle:Games:home.html.twig', array(
            'game' => $game,
        ));
    }

}
