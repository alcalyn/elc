<?php

namespace EL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Score
 * 
 * Contains score data for a player on a game variant,
 * such as elo, elo reliability, wins/losses/draws
 *
 * @ORM\Table(name="el_core_score")
 * @ORM\Entity(repositoryClass="EL\CoreBundle\Repository\ScoreRepository")
 */
class Score
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="EL\CoreBundle\Entity\Player")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;
    
    /**
     * @ORM\ManyToOne(targetEntity="EL\CoreBundle\Entity\GameVariant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $gameVariant;
    
    /**
     * @var float
     * 
     * @ORM\Column(name="elo", type="float")
     */
    private $elo;
    
    /**
     * @var float
     * 
     * @ORM\Column(name="elo_reliability", type="float")
     */
    private $eloReliability;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="wins", type="integer")
     */
    private $wins;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="losses", type="integer")
     */
    private $losses;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="draws", type="integer")
     */
    private $draws;
    
    /**
     * @var float
     * 
     * @ORM\Column(name="points", type="float")
     */
    private $points;
    
    
    public function __construct()
    {
        $this
            ->setElo(Elo::INITIAL_SCORE)
            ->setEloReliability(0.0)
            ->setWins(0)
            ->setLosses(0)
            ->setDraws(0)
            ->setPoints(0)
        ;
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set elo
     *
     * @param float $elo
     * @return Score
     */
    public function setElo($elo)
    {
        $this->elo = $elo;
    
        return $this;
    }

    /**
     * Get elo
     *
     * @return float 
     */
    public function getElo()
    {
        return $this->elo;
    }

    /**
     * Set eloReliability
     *
     * @param float $eloReliability
     * @return Score
     */
    public function setEloReliability($eloReliability)
    {
        $this->eloReliability = $eloReliability;
    
        return $this;
    }

    /**
     * Get eloReliability
     *
     * @return float 
     */
    public function getEloReliability()
    {
        return $this->eloReliability;
    }

    /**
     * Set wins
     *
     * @param integer $wins
     * @return Score
     */
    public function setWins($wins)
    {
        $this->wins = $wins;
    
        return $this;
    }

    /**
     * Get wins
     *
     * @return integer 
     */
    public function getWins()
    {
        return $this->wins;
    }

    /**
     * Set losses
     *
     * @param integer $losses
     * @return Score
     */
    public function setLosses($losses)
    {
        $this->losses = $losses;
    
        return $this;
    }

    /**
     * Get losses
     *
     * @return integer 
     */
    public function getLosses()
    {
        return $this->losses;
    }

    /**
     * Set draws
     *
     * @param integer $draws
     * @return Score
     */
    public function setDraws($draws)
    {
        $this->draws = $draws;
    
        return $this;
    }

    /**
     * Get draws
     *
     * @return integer 
     */
    public function getDraws()
    {
        return $this->draws;
    }
    
    /**
     * Increment wins
     * 
     * @return Score
     */
    public function addWin()
    {
        $this->wins++;
        
        return $this;
    }
    
    /**
     * Increment losses
     * 
     * @return Score
     */
    public function addLoss()
    {
        $this->losses++;
        
        return $this;
    }
    
    /**
     * Increment draws
     * 
     * @return Score
     */
    public function addDraw()
    {
        $this->draws++;
        
        return $this;
    }

    /**
     * Set player
     *
     * @param \EL\CoreBundle\Entity\Player $player
     * @return Score
     */
    public function setPlayer(\EL\CoreBundle\Entity\Player $player)
    {
        $this->player = $player;
    
        return $this;
    }

    /**
     * Get player
     *
     * @return \EL\CoreBundle\Entity\Player 
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set gameVariant
     *
     * @param \EL\CoreBundle\Entity\GameVariant $gameVariant
     * @return Score
     */
    public function setGameVariant(\EL\CoreBundle\Entity\GameVariant $gameVariant)
    {
        $this->gameVariant = $gameVariant;
    
        return $this;
    }

    /**
     * Get gameVariant
     *
     * @return \EL\CoreBundle\Entity\GameVariant 
     */
    public function getGameVariant()
    {
        return $this->gameVariant;
    }

    /**
     * Set points
     *
     * @param float $points
     * @return Score
     */
    public function setPoints($points)
    {
        $this->points = $points;
    
        return $this;
    }

    /**
     * Get points
     *
     * @return float 
     */
    public function getPoints()
    {
        return $this->points;
    }
}
