<?php

namespace EL\Core\Entity;
use EL\Core\Entity\Player;
use EL\Core\Entity\GameVariant;

/**
 * Score
 * 
 * Contains score data for a player on a game variant,
 * such as elo, elo reliability, wins/losses/draws
 */
class Score
{
    /**
     * @var integer
     */
    private $id;
    
    /**
     * @var Player
     * 


     */
    private $player;
    
    /**
     * @var GameVariant
     * 


     */
    private $gameVariant;
    
    /**
     * @var float
     * 

     */
    private $elo;
    
    /**
     * @var float
     * 

     */
    private $eloReliability;
    
    /**
     * @var integer
     * 

     */
    private $wins;
    
    /**
     * @var integer
     * 

     */
    private $losses;
    
    /**
     * @var integer
     * 

     */
    private $draws;
    
    /**
     * @var float
     * 

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
     * Return sum of wins+losses+draws
     * 
     * @return integer
     */
    public function getParties()
    {
        return $this->wins + $this->losses + $this->draws;
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
     * @param \EL\Core\Entity\Player $player
     * @return Score
     */
    public function setPlayer(\EL\Core\Entity\Player $player)
    {
        $this->player = $player;
    
        return $this;
    }

    /**
     * Get player
     *
     * @return \EL\Core\Entity\Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set gameVariant
     *
     * @param \EL\Core\Entity\GameVariant $gameVariant
     * @return Score
     */
    public function setGameVariant(\EL\Core\Entity\GameVariant $gameVariant)
    {
        $this->gameVariant = $gameVariant;
    
        return $this;
    }

    /**
     * Get gameVariant
     *
     * @return \EL\Core\Entity\GameVariant
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
    
    /**
     * Return ratio (wins/losses)
     * 
     * @return integer
     */
    public function getRatio()
    {
        if (0 === $this->losses) {
            return $this->wins;
        } else {
            return $this->wins / $this->losses;
        }
    }
}
