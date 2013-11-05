<?php

namespace EL\ELCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WLDCache
 *
 * @ORM\Table(name="el_core_wld_cache")
 * @ORM\Entity(repositoryClass="EL\ELCoreBundle\Repository\WLDCacheRepository")
 */
class WLDCache
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
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;
    
    /**
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Game")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @var integer
     *
     * @ORM\Column(name="win", type="integer")
     */
    private $win;

    /**
     * @var integer
     *
     * @ORM\Column(name="lose", type="integer")
     */
    private $lose;

    /**
     * @var integer
     *
     * @ORM\Column(name="draw", type="integer")
     */
    private $draw;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_create", type="date")
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
     * Set win
     *
     * @param integer $win
     * @return WLDCache
     */
    public function setWin($win)
    {
        $this->win = $win;
    
        return $this;
    }

    /**
     * Get win
     *
     * @return integer 
     */
    public function getWin()
    {
        return $this->win;
    }

    /**
     * Set lose
     *
     * @param integer $lose
     * @return WLDCache
     */
    public function setLose($lose)
    {
        $this->lose = $lose;
    
        return $this;
    }

    /**
     * Get lose
     *
     * @return integer 
     */
    public function getLose()
    {
        return $this->lose;
    }

    /**
     * Set draw
     *
     * @param integer $draw
     * @return WLDCache
     */
    public function setDraw($draw)
    {
        $this->draw = $draw;
    
        return $this;
    }

    /**
     * Get draw
     *
     * @return integer 
     */
    public function getDraw()
    {
        return $this->draw;
    }

    /**
     * Set dateCreate
     *
     * @param \DateTime $dateCreate
     * @return WLDCache
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
     * @return WLDCache
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
     * Set game
     *
     * @param \EL\ELCoreBundle\Entity\Game $game
     * @return WLDCache
     */
    public function setGame(\EL\ELCoreBundle\Entity\Game $game)
    {
        $this->game = $game;
    
        return $this;
    }

    /**
     * Get game
     *
     * @return \EL\ELCoreBundle\Entity\Game 
     */
    public function getGame()
    {
        return $this->game;
    }
}