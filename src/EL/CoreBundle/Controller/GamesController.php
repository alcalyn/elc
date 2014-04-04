<?php

namespace EL\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use EL\CoreBundle\Entity\Game;
use EL\CoreBundle\Entity\Party;
use EL\CoreBundle\Services\GameService;

class GamesController extends Controller
{
    /**
     * List of games with current player score
     * 
     * @Route(
     *      "/games",
     *      name = "elcore_games_list"
     * )
     * @Template
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

        return array(
            'games'         => $games,
            'categories'    => $categories,
        );
    }


    /**
     * Game page, with image, descrition, ranking, and buttons to start a party
     * 
     * @Route(
     *      "/games/{slug}",
     *      name = "elcore_game_home"
     * )
     * @Template
     */
    public function homeAction($_locale, GameService $gameService)
    {
        $game           = $gameService->getGame();
        $scoreService   = $this->get('el_core.score');
        
        $ranking = $scoreService
                ->getRanking($game, 10)
        ;
        
        $rankingColumns = explode(',', $game->getRankingColumns());
        
        return array(
            'game'              => $game,
            'ranking'           => $ranking,
            'rankingColumns'    => $rankingColumns,
        );
    }
    
    
    /**
     * List of parties for this game
     * 
     * @Route(
     *      "/games/{slug}/games-list",
     *      name = "elcore_game_parties_list"
     * )
     * @Template
     */
    public function partiesListAction($_locale, GameService $gameService)
    {
        $game           = $gameService->getGame();
        $parties        = $gameService->getParties(Party::PREPARATION);
        
        return array(
            'game'      => $game,
            'parties'   => $parties,
        );
    }
    
    
    /**
     * Full ranking board for a game, with pagination, current player position...
     * 
     * @Route(
     *      "/games/{slug}/ranking",
     *      name = "elcore_game_ranking"
     * )
     * @Template
     */
    public function rankingAction($_locale, GameService $gameService)
    {
        $scoreService   = $this->get('el_core.score');
        $game           = $gameService->getGame();
        
        $ranking = $scoreService
                ->getRanking($game, 100)
        ;
        
        $rankingColumns = explode(',', $game->getRankingColumns());
        
        return array(
            'game'              => $game,
            'ranking'           => $ranking,
            'rankingColumns'    => $rankingColumns,
        );
    }
    
    
    /**
     * Simple cms page with rules.
     * Call Extended game template.
     * 
     * @Route(
     *      "/games/{slug}/rules",
     *      name = "elcore_game_rules"
     * )
     * @Template
     */
    public function rulesAction($_locale, GameService $gameService)
    {
        $game = $gameService->getGame();
        
        return array(
            'game' => $game,
        );
    }
}
