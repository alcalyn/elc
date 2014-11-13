<?php

namespace EL\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Elo
 *
 * @ORM\Table(name="el_core_elo")
 * @ORM\Entity(repositoryClass="EL\Bundle\CoreBundle\Repository\EloRepository")
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
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Player
     * 
     * @ORM\ManyToOne(targetEntity="EL\Bundle\CoreBundle\Entity\Player")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;
    
    /**
     * @var GameVariant
     * 
     * @ORM\ManyToOne(targetEntity="EL\Bundle\CoreBundle\Entity\GameVariant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $gameVariant;
    
    /**
     * @var Player
     * 
     * @ORM\ManyToOne(targetEntity="EL\Bundle\CoreBundle\Entity\Player")
     * @ORM\JoinColumn(nullable=true)
     */
    private $opponent;
    
    /**
     * @var Party
     * 
     * @ORM\ManyToOne(targetEntity="EL\Bundle\CoreBundle\Entity\Party")
     * @ORM\JoinColumn(nullable=true)
     */
    private $party;
    
    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_create", type="datetime")
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
     * @param \EL\Bundle\CoreBundle\Entity\Player $player
     * @return Elo
     */
    public function setPlayer(\EL\Bundle\CoreBundle\Entity\Player $player)
    {
        $this->player = $player;
    
        return $this;
    }

    /**
     * Get player
     *
     * @return \EL\Bundle\CoreBundle\Entity\Player 
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set party
     *
     * @param \EL\Bundle\CoreBundle\Entity\Party $party
     * @return Elo
     */
    public function setParty(\EL\Bundle\CoreBundle\Entity\Party $party = null)
    {
        $this->party = $party;
    
        return $this;
    }

    /**
     * Get party
     *
     * @return \EL\Bundle\CoreBundle\Entity\Party 
     */
    public function getParty()
    {
        return $this->party;
    }

    /**
     * Set gameVariant
     *
     * @param \EL\Bundle\CoreBundle\Entity\GameVariant $gameVariant
     * @return Elo
     */
    public function setGameVariant(\EL\Bundle\CoreBundle\Entity\GameVariant $gameVariant)
    {
        $this->gameVariant = $gameVariant;
    
        return $this;
    }

    /**
     * Get gameVariant
     *
     * @return \EL\Bundle\CoreBundle\Entity\GameVariant 
     */
    public function getGameVariant()
    {
        return $this->gameVariant;
    }

    /**
     * Set opponent
     *
     * @param \EL\Bundle\CoreBundle\Entity\Player $opponent
     * @return Elo
     */
    public function setOpponent(\EL\Bundle\CoreBundle\Entity\Player $opponent)
    {
        $this->opponent = $opponent;
    
        return $this;
    }

    /**
     * Get opponent
     *
     * @return \EL\Bundle\CoreBundle\Entity\Player 
     */
    public function getOpponent()
    {
        return $this->opponent;
    }
}
