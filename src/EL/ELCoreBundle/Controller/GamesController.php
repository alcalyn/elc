<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class GamesController extends Controller
{
    
    
    /**
     * @Route(
     *      "/games",
     *      name = "elcore_games_list"
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
     *      "/games/{slug}",
     *      name = "elcore_game_home"
     * )
     */
    public function homeAction($_locale, $slug)
    {
        $game_service= $this->get('el_core.game');
        
        $game = $game_service
                ->setGameBySlug($slug, $_locale)
                ->getGame();
        
        return $this->render('ELCoreBundle:Games:home.html.twig', array(
            'game' => $game,
        ));
    }
    
    
    /**
     * @Route(
     *      "/games/{slug}/ranking",
     *      name = "elcore_game_ranking"
     * )
     */
    public function rankingAction($_locale, $slug)
    {
        $game_service= $this->get('el_core.game');
        
        $game = $game_service
                ->setGameBySlug($slug, $_locale)
                ->getGame();
        
        return $this->render('ELCoreBundle:Games:ranking.html.twig', array(
            'game' => $game,
        ));
    }
    
    
    /**
     * @Route(
     *      "/games/{slug}/rules",
     *      name = "elcore_game_rules"
     * )
     */
    public function rulesAction($_locale, $slug)
    {
        $game_service= $this->get('el_core.game');
        
        $game = $game_service
                ->setGameBySlug($slug, $_locale)
                ->getGame();
        
        return $this->render('ELCoreBundle:Games:rank.html.twig', array(
            'game' => $game,
        ));
    }

}
