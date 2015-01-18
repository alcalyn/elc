<?php

namespace EL\Core\Service;

use Doctrine\ORM\EntityManager;
use EL\Core\Entity\Player;
use EL\Core\Entity\Game;
use EL\Core\Entity\GameVariant;
use EL\Core\Entity\Party;
use EL\Core\Entity\Points;
use EL\Core\Service\ScoreService;

class PointsService extends ScoreService
{
    
    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }
    
    /**
     * Add a points value in time for history
     * 
     * @param \EL\Core\Entity\Player $player
     * @param \EL\Core\Entity\GameVariant $gameVariant
     * @param \EL\Core\Entity\Party $party
     * @param double $value
     * 
     * @return Points
     */
    private function createStatistic(Player $player, $value, GameVariant $gameVariant, Party $party = null)
    {
        $points = new Points();
        
        return $points
                ->setPlayer($player)
                ->setGameVariant($gameVariant)
                ->setValue($value)
                ->setParty($party)
                ->setDateCreate(new \DateTime())
        ;
    }
    
    /**
     * Update score value for a player on a Game or GameVariant
     * 
     * Example of use :
     *      $this->get('el_core.score.points')->update($player, $game, 1000);
     * 
     * @param \EL\Core\Entity\Player $player
     * @param Game|GameVariant $game
     * @param float $points
     * @param \EL\Core\Entity\Party $party
     * 
     * @return \EL\Core\Entity\Score
     */
    public function update(Player $player, $game, $points, Party $party = null)
    {
        /**
         * Initialize variables
         */
        $gameVariant    = $this->asGameVariant($game);
        $scoreData      = $this->getScoreData($player, $gameVariant);
        
        /**
         * Update points
         */
        $scoreData->setPoints($points);
        
        /**
         * Add a points value in time
         */
        $stat = $this->createStatistic($player, $points, $gameVariant, $party);
        
        /**
         * Save in database
         */
        $this->em->persist($stat);
        
        return $scoreData;
    }
}
