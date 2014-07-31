<?php

namespace EL\CoreBundle\Services;

use Doctrine\ORM\EntityManager;
use EL\CoreBundle\Exception\ELCoreException;
use EL\CoreBundle\Entity\Player;
use EL\CoreBundle\Entity\Elo;
use EL\CoreBundle\Entity\Game;
use EL\CoreBundle\Entity\GameVariant;
use EL\CoreBundle\Services\ScoreService;
use EL\CoreBundle\Entity\Party;

class EloService extends ScoreService
{
    
    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }
    
    /**
     * Return current elo score for a player on a game or game variant.
     * You should use ScoreService::get() if you need others score statistics too (WLD)
     * 
     * @param \EL\CoreBundle\Entity\Player $player
     * @param Game|GameVariant $game
     * 
     * @return double
     */
    public function getElo(Player $player, $game)
    {
        $score = $this->getScoreData($player, $game);
        
        return $score->getElo();
    }
    
    /**
     * Create a statistic point in time
     * 
     * @param \EL\CoreBundle\Entity\Player $player
     * @param float $newElo
     * @param \EL\CoreBundle\Entity\GameVariant $gameVariant
     * @param \EL\CoreBundle\Entity\Party $party
     * 
     * @return Elo created
     */
    private function createStatistic(Player $player, Player $opponent, $newElo, GameVariant $gameVariant, Party $party = null)
    {
        $elo = new Elo();
        
        return $elo
            ->setPlayer($player)
            ->setGameVariant($gameVariant)
            ->setOpponent($opponent)
            ->setParty($party)
            ->setValue($newElo)
            ->setDateCreate(new \DateTime())
        ;
    }
    
    /**
     * Notify an elo update for $p0 and $p1, on a game or game variant $game.
     * Indicate in $win 0, 0.5 or 1
     * 
     * Example of use :
     *      $this->get('el_core.score.elo')->win($p0, $p1, $game);
     *      $this->get('el_core.score.elo')->update($p0, $p1, $game, $party, 1);
     * 
     * @param \EL\CoreBundle\Entity\Player $p0
     * @param \EL\CoreBundle\Entity\Player $p1
     * @param Game|GameVariant $game
     * @param Party $party in which update is linked to
     * @param integer $win :
     *      1   for $p0 wins,
     *      0   for $p1 wins,
     *      0.5 for draw
     * 
     * @return array of elos gain for p0 and p1
     */
    public function update(Player $p0, Player $p1, $game, Party $party = null, $win = 1)
    {
        if (($win < 0) || ($win > 1)) {
            throw new ELCoreException('win must be in range [0;1]');
        }
        
        /**
         * Initialize variables
         */
        $gameVariant        = $this->asGameVariant($game);
        $scoreData0         = $this->getScoreData($p0, $gameVariant);
        $scoreData1         = $this->getScoreData($p1, $gameVariant);
        $elo0               = $scoreData0->getElo();
        $elo1               = $scoreData1->getElo();
        $eloReliability0    = $scoreData0->getEloReliability();
        $eloReliability1    = $scoreData1->getEloReliability();
        
        /**
         * Calculate probability $p0 have to beat $p1
         */
        $proba = $this->proba($elo0, $elo1);
        
        /**
         * Calculate elo changement
         */
        $update0 = $win - $proba;
        $update1 = -$update0;
        
        /**
         * Calculate local reliability to avoid 0 and 0 reliability for new players
         * (if two new players have 0 and 0.1 reliability, they are rectified to 0.9 and 1)
         */
        $reliabilityRectification = 1 - max($eloReliability0, $eloReliability1);
        
        $reliability0 = $eloReliability0 + $reliabilityRectification;
        $reliability1 = $eloReliability1 + $reliabilityRectification;
        
        /**
         * Apply coefs K-factor and reliability of each other
         */
        $update0 *= Elo::K * $reliability1 ;
        $update1 *= Elo::K * $reliability0 ;
        
        /**
         * Apply updates
         */
        $elo0 += $update0;
        $elo1 += $update1;
        
        /**
         * Increase reliabilities
         */
        $reliabilityGain = 1 / Elo::PARTY_RELIABILITY;
        $eloReliability0 += $reliabilityGain;
        $eloReliability1 += $reliabilityGain;
        
        /**
         * Update scores data
         */
        $scoreData0->setElo($elo0);
        $scoreData1->setElo($elo1);
        $scoreData0->setEloReliability(min($eloReliability0, 1));
        $scoreData1->setEloReliability(min($eloReliability1, 1));
        
        /**
         * Create statistics
         */
        $stat0 = $this->createStatistic($p0, $p1, $elo0, $gameVariant, $party);
        $stat1 = $this->createStatistic($p1, $p0, $elo1, $gameVariant, $party);
        
        /**
         * Save into database
         */
        $this->em->persist($stat0);
        $this->em->persist($stat1);
        
        return array(
            'p0' => $update0,
            'p1' => $update1,
        );
    }
    
    /**
     * $p0 beat $p1
     * 
     * @param \EL\CoreBundle\Entity\Player $p0
     * @param \EL\CoreBundle\Entity\Player $p1
     * @param Game|GameVariant $game
     * @param Party $party in which update is linked to
     * 
     * @return array
     */
    public function win(Player $p0, Player $p1, $game, Party $party = null)
    {
        return $this->update($p0, $p1, $game, $party, 1);
    }
    
    /**
     * $p0 beaten by $p1
     * 
     * @param \EL\CoreBundle\Entity\Player $p0
     * @param \EL\CoreBundle\Entity\Player $p1
     * @param Game|GameVariant $game
     * @param Party $party in which update is linked to
     * 
     * @return array
     */
    public function lose(Player $p0, Player $p1, $game, Party $party = null)
    {
        return $this->update($p0, $p1, $game, $party, 0);
    }
    
    /**
     * $p0 and $p1 draw a party
     * 
     * @param \EL\CoreBundle\Entity\Player $p0
     * @param \EL\CoreBundle\Entity\Player $p1
     * @param Game|GameVariant $game
     * @param Party $party in which update is linked to
     * 
     * @return array
     */
    public function draw(Player $p0, Player $p1, $game, Party $party = null)
    {
        return $this->update($p0, $p1, $game, $party, 0.5);
    }
    
    /**
     * Return probability rate that $elo0 beat $elo1
     * 
     * @param integer $elo0
     * @param integer $elo1
     * 
     * @return float
     */
    public function proba($elo0, $elo1)
    {
        return 1 / (1 + pow(10, ($elo1 - $elo0) / 400)) ;
    }
}
