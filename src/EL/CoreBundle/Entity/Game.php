<?php

namespace EL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table(name="el_core_game")
 * @ORM\Entity(repositoryClass="EL\CoreBundle\Repository\GameRepository")
 */
class Game extends AbstractLangEntity implements \JsonSerializable
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
     * @var EL\CoreBundle\Category
     * 
     * @ORM\ManyToOne(targetEntity="EL\CoreBundle\Entity\Category", inversedBy="games")
     * @ORM\JoinColumn(nullable=true)
     */
    private $category;
    
    /**
     * @ORM\OneToMany(targetEntity="EL\CoreBundle\Entity\GameLang", mappedBy="game")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $langs;

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
     * Mode room : players can join
     * even if party is already started and running
     * (e.g Poker...)
     *
     * @ORM\Column(name="room", type="boolean")
     */
    private $room;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->langs = new \Doctrine\Common\Collections\ArrayCollection();
        $this
                ->setRoom(false)
                ->setVisible(true);
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
     * @param \EL\CoreBundle\Entity\Category $category
     * @return Game
     */
    public function setCategory(\EL\CoreBundle\Entity\Category $category = null)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \EL\CoreBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * @return string displayable player number :
     *      "2" or "2 - 4"
     */
    public function getNbPlayer()
    {
        if ($this->getNbplayerMin() == $this->getNbplayerMax()) {
            return $this->getNbplayerMin();
        } else {
            return implode(' - ', array(
                $this->getNbplayerMin(),
                $this->getNbplayerMax(),
            ));
        }
    }
    
    /**
     * Add langs
     *
     * @param \EL\CoreBundle\Entity\GameLang $langs
     * @return Game
     */
    public function addLang(\EL\CoreBundle\Entity\GameLang $langs)
    {
        $this->langs[] = $langs;
    
        return $this;
    }

    /**
     * Remove langs
     *
     * @param \EL\CoreBundle\Entity\GameLang $langs
     */
    public function removeLang(\EL\CoreBundle\Entity\GameLang $langs)
    {
        $this->langs->removeElement($langs);
    }

    /**
     * Get langs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLangs()
    {
        return $this->langs;
    }

    /**
     * Set room
     *
     * @param boolean $room
     * @return Game
     */
    public function setRoom($room)
    {
        $this->room = $room;
    
        return $this;
    }

    /**
     * Get room
     *
     * @return boolean 
     */
    public function getRoom()
    {
        return $this->room;
    }
    
    
    public function jsonSerialize()
    {
        return array(
            'id'            => $this->getId(),
            'name'          => $this->getName(),
            'title'         => $this->getTitle(),
            'nbPlayerMin' => $this->getNbplayerMin(),
            'nbPlayerMax' => $this->getNbplayerMax(),
            'room'          => $this->getRoom(),
        );
    }
}
