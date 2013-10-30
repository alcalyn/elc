<?php

namespace EL\ELCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table(name="el_core_game")
 * @ORM\Entity(repositoryClass="EL\ELCoreBundle\Repositories\GameRepository")
 */
class Game
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
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Category")
     * @ORM\JoinColumn(nullable=true)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=31)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbplayer_min", type="integer")
     */
    private $nbplayerMin;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbplayer_max", type="integer")
     */
    private $nbplayerMax;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible;


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
     * Set name
     *
     * @param string $name
     * @return Game
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set nbplayerMin
     *
     * @param integer $nbplayerMin
     * @return Game
     */
    public function setNbplayerMin($nbplayerMin)
    {
        $this->nbplayerMin = $nbplayerMin;
    
        return $this;
    }

    /**
     * Get nbplayerMin
     *
     * @return integer 
     */
    public function getNbplayerMin()
    {
        return $this->nbplayerMin;
    }

    /**
     * Set nbplayerMax
     *
     * @param integer $nbplayerMax
     * @return Game
     */
    public function setNbplayerMax($nbplayerMax)
    {
        $this->nbplayerMax = $nbplayerMax;
    
        return $this;
    }

    /**
     * Get nbplayerMax
     *
     * @return integer 
     */
    public function getNbplayerMax()
    {
        return $this->nbplayerMax;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return Game
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set category
     *
     * @param \EL\ELCoreBundle\Entity\Category $category
     * @return Game
     */
    public function setCategory(\EL\ELCoreBundle\Entity\Category $category = null)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \EL\ELCoreBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
}