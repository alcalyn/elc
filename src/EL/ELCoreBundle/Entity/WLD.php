<?php

namespace EL\ELCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WLD
 * Save a win/loss/draw party in time
 *
 * @ORM\Table(name="el_core_wld")
 * @ORM\Entity(repositoryClass="EL\ELCoreBundle\Repository\WLDRepository")
 */
class WLD
{
    const WIN   = 1;
    const LOSS  = 2;
    const DRAW  = 3;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Player")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;
    
    /**
     * @var GameVariant
     * 
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\GameVariant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $gameVariant;
    
    /**
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Party")
     * @ORM\JoinColumn(nullable=true)
     */
    private $party;

    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="smallint")
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
     * @param \EL\ELCoreBundle\Entity\Player $player
     * @return WLD
     */
    public function setPlayer(\EL\ELCoreBundle\Entity\Player $player)
    {
        $this->player = $player;
    
        return $this;
    }

    /**
     * Get player
     *
     * @return \EL\ELCoreBundle\Entity\Player 
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set party
     *
     * @param \EL\ELCoreBundle\Entity\Party $party
     * @return WLD
     */
    public function setParty(\EL\ELCoreBundle\Entity\Party $party = null)
    {
        $this->party = $party;
    
        return $this;
    }

    /**
     * Get party
     *
     * @return \EL\ELCoreBundle\Entity\Party 
     */
    public function getParty()
    {
        return $this->party;
    }

    /**
     * Set gameVariant
     *
     * @param \EL\ELCoreBundle\Entity\GameVariant $gameVariant
     * @return WLD
     */
    public function setGameVariant(\EL\ELCoreBundle\Entity\GameVariant $gameVariant)
    {
        $this->gameVariant = $gameVariant;
    
        return $this;
    }

    /**
     * Get gameVariant
     *
     * @return \EL\ELCoreBundle\Entity\GameVariant 
     */
    public function getGameVariant()
    {
        return $this->gameVariant;
    }
}
