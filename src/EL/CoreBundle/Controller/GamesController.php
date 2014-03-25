<?php

namespace EL\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class GamesController extends Controller
{
    /**
     * List of games with current player score
     * 
     * @Route(
     *      "/games",
     *      name = "elcore_games_list"
     * )
     */
    public function listAction($_locale)
    {
        $em             = $this->getDoctrine()->getManager();
        $sessionService = $this->get('el_core.session');
        
        $categories = $em
                ->getRepository('CoreBundle:Category')
                ->findAllForLang($_locale)
        ;
        
        $games = $em
                ->getRepository('CoreBundle:Game')
                ->findAllByLang($_locale, $sessionService->getPlayer())
        ;

        return $this->render('CoreBundle:Games:list.html.twig', array(
            'games'         => $games,
            'categories'    => $categories,
        ));
    }


    /**
     * Game page, with image, descrition, ranking, and buttons to start a party
     * 
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
                ->getRanking($game, 10)
        ;
        
        $rankingColumns = explode(',', $game->getRankingColumns());
        
        return $this->render('CoreBundle:Games:home.html.twig', array(
            'game'              => $game,
            'ranking'           => $ranking,
            'rankingColumns'    => $rankingColumns,
        ));
    }
    
    
    /**
     * Full ranking board for a game, with pagination, current player position...
     * 
     * @Route(
     *      "/games/{slug}/ranking",
     *      name = "elcore_game_ranking"
     * )
     */
    public function rankingAction($_locale, $slug)
    {
        $gameService    = $this->get('el_core.game');
        $scoreService   = $this->get('el_core.score');
        
        $game = $gameService
                ->setGameBySlug($slug, $_locale)
                ->getGame()
        ;
        
        $ranking = $scoreService
                ->getRanking($game, 100)
        ;
        
        $rankingColumns = explode(',', $game->getRankingColumns());
        
        return $this->render('CoreBundle:Games:ranking.html.twig', array(
            'game'              => $game,
            'ranking'           => $ranking,
            'rankingColumns'    => $rankingColumns,
        ));
    }
    
    
    /**
     * Simple cms page with rules.
     * Call Extended game template.
     * 
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
