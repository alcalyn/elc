<?php

namespace EL\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Elo
 */
class Elo
{
    /**
     * Each player starts at this score
     */
    const INITIAL_SCORE = 1500;
    
    /**
     * Parties number needed to reach 100% relability
     */
    const PARTY_RELIABILITY = 10;
    
    /**
     * K factor, greater K is, greater will be elo updates.
     */
    const K = 16;
    
    
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
     * @var Player
     * 


     */
    private $opponent;
    
    /**
     * @var Party
     * 


     */
    private $party;
    
    /**
     * @var float
     */
    private $value;

    /**
     * @var \DateTime
     */
    private $dateCreate;


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
     * Set value
     *
     * @param float $value
     * @return Elo
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set dateCreate
     *
     * @param \DateTime $dateCreate
     * @return Elo
     */
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;
    
        return $this;
    }

    /**
     * Get dateCreate
     *
     * @return \DateTime
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * Set player
     *
     * @param \EL\Core\Entity\Player $player
     * @return Elo
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
     * Set party
     *
     * @param \EL\Core\Entity\Party $party
     * @return Elo
     */
    public function setParty(\EL\Core\Entity\Party $party = null)
    {
        $this->party = $party;
    
        return $this;
    }

    /**
     * Get party
     *
     * @return \EL\Core\Entity\Party
     */
    public function getParty()
    {
        return $this->party;
    }

    /**
     * Set gameVariant
     *
     * @param \EL\Core\Entity\GameVariant $gameVariant
     * @return Elo
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
     * Set opponent
     *
     * @param \EL\Core\Entity\Player $opponent
     * @return Elo
     */
    public function setOpponent(\EL\Core\Entity\Player $opponent)
    {
        $this->opponent = $opponent;
    
        return $this;
    }

    /**
     * Get opponent
     *
     * @return \EL\Core\Entity\Player
     */
    public function getOpponent()
    {
        return $this->opponent;
    }
}
