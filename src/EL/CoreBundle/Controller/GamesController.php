<?php

namespace EL\CoreBundle\Controller;

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
        $em             = $this->getDoctrine()->getManager();
        $sessionService = $this->get('el_core.session');
        
        $games = $em
                ->getRepository('CoreBundle:Game')
                ->findAllByLang($_locale, $sessionService->getPlayer())
        ;

        return $this->render('CoreBundle:Games:list.html.twig', array(
            'games'     => $games,
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
        $gameService    = $this->get('el_core.game');
        $scoreService   = $this->get('el_core.score');
        
        $game = $gameService
                ->setGameBySlug($slug, $_locale)
                ->getGame()
        ;
        
        $ranking = $scoreService
                ->getRanking($game, 0, 10)
        ;
        
        $rankingColumns = explode(',', $game->getRankingColumns());
        
        return $this->render('CoreBundle:Games:home.html.twig', array(
            'game'              => $game,
            'ranking'           => $ranking,
            'rankingColumns'    => $rankingColumns,
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
        $gameService= $this->get('el_core.game');
        
        $game = $gameService
                ->setGameBySlug($slug, $_locale)
                ->getGame();
        
        return $this->render('CoreBundle:Games:ranking.html.twig', array(
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
        $gameService= $this->get('el_core.game');
        
        $game = $gameService
                ->setGameBySlug($slug, $_locale)
                ->getGame();
        
        return $this->render('CoreBundle:Games:rank.html.twig', array(
            'game' => $game,
        ));
    }
}
