<?php

namespace EL\ELCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Slot
 *
 * @ORM\Table(name="el_core_slot")
 * @ORM\Entity(repositoryClass="EL\ELCoreBundle\Repository\SlotRepository")
 */
class Slot
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
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Player")
     * @ORM\JoinColumn(nullable=true)
     */
    private $player;
    
    /**
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Party", inversedBy="slots")
     * @ORM\JoinColumn(nullable=false)
     */
    private $party;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="smallint")
     */
    private $position;

    /**
     * @var boolean
     *
     * @ORM\Column(name="open", type="boolean")
     */
    private $open;

    /**
     * @var float
     *
     * @ORM\Column(name="score", type="float")
     */
    private $score;


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
     * Set player
     *
     * @param \EL\ELCoreBundle\Entity\Player $player
     * @return Slot
     */
    public function setPlayer(\EL\ELCoreBundle\Entity\Player $player = null)
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
     * @return Slot
     */
    public function setParty(\EL\ELCoreBundle\Entity\Party $party)
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
}