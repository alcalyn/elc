<?php

namespace EL\ELCoreBundle\Services;

use Doctrine\ORM\EntityManager;
use EL\ELCoreBundle\Entity\Player;
use EL\ELCoreBundle\Entity\Game;
use EL\ELCoreBundle\Entity\GameVariant;
use EL\ELCoreBundle\Services\ScoreService;
use EL\ELCoreBundle\Entity\Party;
use EL\ELCoreBundle\Entity\Points;

class PointsService extends ScoreService
{
    
    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }
    
    /**
     * Add a points value in time for history
     * 
     * @param \EL\ELCoreBundle\Entity\Player $player
     * @param \EL\ELCoreBundle\Entity\Points $points
     * @param \EL\ELCoreBundle\Entity\GameVariant $gameVariant
     * @param \EL\ELCoreBundle\Entity\Party $party
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
     * @param \EL\ELCoreBundle\Entity\Player $player
     * @param Game|GameVariant $game
     * @param float $points
     * @param \EL\ELCoreBundle\Entity\Party $party
     * 
     * @return \EL\ELCoreBundle\Entity\Score
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
        
        $this->em->flush();
        
        return $scoreData;
    }
}
