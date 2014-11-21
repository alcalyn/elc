<?php

namespace EL\Core\Entity;

/**
 * Points
 */
class Points
{
    /**
     * @var integer
     */
    private $id;
    
    /**
     * @var Player
     */
    private $player;
    
    /**
     * @var GameVariant
     */
    private $gameVariant;
    
    /**
     * @var Party
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
     * @return Points
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
     * @return Points
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
     * @param Player $player
     * @return Points
     */
    public function setPlayer(Player $player)
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
     * Set gameVariant
     *
     * @param GameVariant $gameVariant
     * @return Points
     */
    public function setGameVariant(GameVariant $gameVariant)
    {
        $this->gameVariant = $gameVariant;
    
        return $this;
    }

    /**
     * Get gameVariant
     *
     * @return GameVariant
     */
    public function getGameVariant()
    {
        return $this->gameVariant;
    }

    /**
     * Set party
     *
     * @param Party $party
     * @return Points
     */
    public function setParty(Party $party = null)
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
}
