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
     * @var \Doctrine\Common\Collections\ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="EL\CoreBundle\Entity\GameLang", mappedBy="game")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $langs;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="EL\CoreBundle\Entity\GameVariant", mappedBy="game")
     * @ORM\JoinColumn(nullable=true)
     */
    private $gameVariants;

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
     * @var string
     * 
     * Which colums to display in rank board
     * under the form col,col2,col3 ...
     *
     * @ORM\Column(
     *      name="ranking_columns",
     *      type="string",
     *      length=63,
     *      options={"default" = "parties,wins,losses,draws,ratio,elo,score"}
     * )
     */
    private $rankingColumns;
    
    /**
     * @var string
     * 
     * Ordering strategy of rank board
     * under the form col:a,col3:d
     * 
     * 'a' for ascendant, 'd' for descendant
     *
     * @ORM\Column(name="ranking_order", type="string", length=31, options={"default" = "wins:d,draws:d"})
     */
    private $rankingOrder;

    /**
     * @var string
     * 
     * Main statistics to display with a player for this game
     *
     * @ORM\Column(name="ranking_reference", type="string", length=15, options={"default" = "wins"})
     */
    private $rankingReference;
    
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
        $this->langs        = new \Doctrine\Common\Collections\ArrayCollection();
        $this->gameVariants = new \Doctrine\Common\Collections\ArrayCollection();
        
        $this
                ->setRoom(false)
                ->setVisible(true)
                ->setRankingColumns('parties,wins,losses,draws,ratio,elo,score')
                ->setRankingOrder('wins:d,draws:d')
                ->setRankingReference('wins')
        ;
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

    /**
     * Set rankingColumns
     *
     * @param string $rankingColumns
     * @return Game
     */
    public function setRankingColumns($rankingColumns)
    {
        $this->rankingColumns = $rankingColumns;
    
        return $this;
    }

    /**
     * Get rankingColumns
     *
     * @return string 
     */
    public function getRankingColumns()
    {
        return $this->rankingColumns;
    }

    /**
     * Set rankingOrder
     *
     * @param string $rankingOrder
     * @return Game
     */
    public function setRankingOrder($rankingOrder)
    {
        $this->rankingOrder = $rankingOrder;
    
        return $this;
    }

    /**
     * Get rankingOrder
     *
     * @return string 
     */
    public function getRankingOrder()
    {
        return $this->rankingOrder;
    }

    /**
     * Set rankingReference
     *
     * @param string $rankingReference
     * @return Game
     */
    public function setRankingReference($rankingReference)
    {
        $this->rankingReference = $rankingReference;
    
        return $this;
    }

    /**
     * Get rankingReference
     *
     * @return string 
     */
    public function getRankingReference()
    {
        return $this->rankingReference;
    }

    /**
     * Add gameVariants
     *
     * @param \EL\CoreBundle\Entity\GameVariant $gameVariants
     * @return Game
     */
    public function addGameVariant(\EL\CoreBundle\Entity\GameVariant $gameVariants)
    {
        $this->gameVariants[] = $gameVariants;
    
        return $this;
    }

    /**
     * Remove gameVariants
     *
     * @param \EL\CoreBundle\Entity\GameVariant $gameVariants
     */
    public function removeGameVariant(\EL\CoreBundle\Entity\GameVariant $gameVariants)
    {
        $this->gameVariants->removeElement($gameVariants);
    }

    /**
     * Get gameVariants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGameVariants()
    {
        return $this->gameVariants;
    }
    
    /**
     * Implements JsonSerialize
     * 
     * @return array
     */
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
