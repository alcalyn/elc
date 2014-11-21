<?php

namespace EL\Core\Entity;

/**
 * Slot
 */
class Slot implements \JsonSerializable
{
    /**
     * @var integer
     */
    private $id;
    
    private $player;
    
    private $party;
    
    /**
     * @var integer
     */
    private $position;

    /**
     * @var boolean
     */
    private $open;

    /**
     * @var float
     */
    private $score;
    
    
    public function __construct()
    {
        $this->score = 0;
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
     * Set position
     *
     * @param integer $position
     * @return Slot
     */
    public function setPosition($position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set open
     *
     * @param boolean $open
     * @return Slot
     */
    public function setOpen($open)
    {
        $this->open = $open;
    
        return $this;
    }

    /**
     * Get open
     *
     * @return boolean
     */
    public function getOpen()
    {
        return $this->open;
    }

    /**
     * Set score
     *
     * @param float $score
     * @return Slot
     */
    public function setScore($score)
    {
        $this->score = $score;
    
        return $this;
    }

    /**
     * Get score
     *
     * @return float
     */
    public function getScore()
    {
        return $this->score;
    }
    
    /**
     * Add $n to score
     * 
     * @return Slot
     */
    public function addScore($n = 1)
    {
        $this->score += $n;
        return $this;
    }

    /**
     * Set player
     *
     * @param Player $player
     * @return Slot
     */
    public function setPlayer(Player $player = null)
    {
        $this->player = $player;
    
        return $this;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Has player
     *
     * @return boolean
     */
    public function hasPlayer()
    {
        return !is_null($this->player);
    }

    /**
     * Set party
     *
     * @param Party $party
     * @return Slot
     */
    public function setParty(Party $party)
    {
        $this->party = $party;
    
        return $this;
    }

    /**
     * Get party
     *
     * @return Party
     */
    public function getParty()
    {
        return $this->party;
    }
    
    /**
     * True if a player can join using this slot
     * 
     * @return boolean
     */
    public function isFree()
    {
        return
                $this->getOpen() &&
                is_null($this->getPlayer());
    }
    
    /**
     * Implements JsonSerializable
     * 
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'id'        => $this->getId(),
            'player'    => is_null($this->getPlayer()) ? null : $this->getPlayer()->jsonSerialize(),
            'position'  => $this->getPosition(),
            'open'      => $this->getOpen(),
            'score'     => $this->getScore(),
        );
    }
}
