<?php

namespace EL\Core\Entity;

/**
 * WLD
 * Save a win/loss/draw party in time
 */
class WLD
{
    const WIN   = 1;
    const LOSS  = 2;
    const DRAW  = 3;
    
    
    /**
     * @var integer
     */
    private $id;
    
    private $player;
    
    /**
     * @var GameVariant
     * 


     */
    private $gameVariant;
    
    private $party;

    /**
     * @var integer
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
     * @param integer $value
     * @return WLD
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set dateCreate
     *
     * @param \DateTime $dateCreate
     * @return WLD
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
     * @return WLD
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
     * @return WLD
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
     * @return WLD
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
}
