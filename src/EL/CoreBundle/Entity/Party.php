<?php

namespace EL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EL\CoreBundle\Entity\Slot;

/**
 * Party
 *
 * @ORM\Table(name="el_core_party")
 * @ORM\Entity(repositoryClass="EL\CoreBundle\Repository\PartyRepository")
 */
class Party implements \JsonSerializable
{
    
    /**
     * Party created, waiting for player, order slot
     * @var integer
     */
    const PREPARATION   = 1;
    
    /**
     * Host has started, party began in n seconds
     * @var integer
     */
    const STARTING      = 2;
    
    /**
     * Party is currently running, players are playing
     * @var integer
     */
    const ACTIVE        = 3;
    
    /**
     * Party has ended, we can only see scores
     * @var integer
     */
    const ENDED         = 4;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Game
     * 
     * @ORM\ManyToOne(targetEntity="EL\CoreBundle\Entity\Game")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;
    
    /**
     * @var Player
     * 
     * @ORM\ManyToOne(targetEntity="EL\CoreBundle\Entity\Player")
     * @ORM\JoinColumn(nullable=true)
     */
    private $host;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="EL\CoreBundle\Entity\Slot", mappedBy="party")
     */
    private $slots;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=63)
     */
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=63)
     */
    private $slug;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="smallint")
     */
    private $state;

    /**
     * @var boolean
     *
     * @ORM\Column(name="private", type="boolean")
     */
    private $private;
    
    /**
     * @var boolean
     * 
     * If the game is room mode,
     * set this parameter to false
     * to disable room mode on this party
     *
     * @ORM\Column(name="room", type="boolean")
     */
    private $room;
    
    /**
     * @var boolean
     * 
     * If the chat is enabled for this party
     * 
     * @ORM\Column(name="disallow_chat", type="boolean")
     */
    private $disallowChat;
    
    /**
     * @var boolean
     * 
     * If this party allows observers
     * 
     * @ORM\Column(name="disallow_observers", type="boolean")
     */
    private $disallowObservers;
    
    /**
     * Contains party which is the clone of this
     * created by the player who has remade an older party
     * 
     * @ORM\OneToOne(targetEntity="EL\CoreBundle\Entity\Party")
     * @ORM\JoinColumn(nullable=true)
     */
    private $remake;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_create", type="datetime")
     */
    private $dateCreate;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_started", type="datetime", nullable=true)
     */
    private $dateStarted;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_ended", type="datetime", nullable=true)
     */
    private $dateEnded;
    
    
    
    public function __construct()
    {
        $this
                ->setState(self::PREPARATION)
                ->setRoom(true)
                ->setPrivate(false)
                ->setDisallowChat(false)
                ->setDisallowObservers(false)
                ->setDateCreate(new \DateTime())
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
     * Set title
     *
     * @param string $title
     * @return Party
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Party
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Party
     */
    public function setState($state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set private
     *
     * @param boolean $private
     * @return Party
     */
    public function setPrivate($private)
    {
        $this->private = $private;
    
        return $this;
    }

    /**
     * Get private
     *
     * @return boolean 
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * Set room
     *
     * @param boolean $room
     * @return Party
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
     * Set disallowChat
     *
     * @param boolean $disallowChat
     * @return Party
     */
    public function setDisallowChat($disallowChat)
    {
        $this->disallowChat = $disallowChat;
    
        return $this;
    }

    /**
     * Get disallowChat
     *
     * @return boolean 
     */
    public function getDisallowChat()
    {
        return $this->disallowChat;
    }

    /**
     * Set disallowObservers
     *
     * @param boolean $disallowObservers
     * @return Party
     */
    public function setDisallowObservers($disallowObservers)
    {
        $this->disallowObservers = $disallowObservers;
    
        return $this;
    }

    /**
     * Get disallowObservers
     *
     * @return boolean 
     */
    public function getDisallowObservers()
    {
        return $this->disallowObservers;
    }

    /**
     * Set dateCreate
     *
     * @param \DateTime $dateCreate
     * @return Party
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
     * Set dateStarted
     *
     * @param \DateTime $dateStarted
     * @return Party
     */
    public function setDateStarted($dateStarted)
    {
        $this->dateStarted = $dateStarted;
    
        return $this;
    }

    /**
     * Get dateStarted
     *
     * @return \DateTime 
     */
    public function getDateStarted()
    {
        return $this->dateStarted;
    }

    /**
     * Set dateEnded
     *
     * @param \DateTime $dateEnded
     * @return Party
     */
    public function setDateEnded($dateEnded)
    {
        $this->dateEnded = $dateEnded;
    
        return $this;
    }

    /**
     * Get dateEnded
     *
     * @return \DateTime 
     */
    public function getDateEnded()
    {
        return $this->dateEnded;
    }

    /**
     * Set game
     *
     * @param \EL\CoreBundle\Entity\Game $game
     * @return Party
     */
    public function setGame(\EL\CoreBundle\Entity\Game $game)
    {
        $this->game = $game;
    
        return $this;
    }

    /**
     * Get game
     *
     * @return \EL\CoreBundle\Entity\Game 
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set host
     *
     * @param \EL\CoreBundle\Entity\Player $host
     * @return Party
     */
    public function setHost(\EL\CoreBundle\Entity\Player $host = null)
    {
        $this->host = $host;
    
        return $this;
    }

    /**
     * Get host
     *
     * @return \EL\CoreBundle\Entity\Player 
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Add slots
     *
     * @param \EL\CoreBundle\Entity\Slot $slots
     * @return Party
     */
    public function addSlot(\EL\CoreBundle\Entity\Slot $slots)
    {
        $this->slots[] = $slots;
    
        return $this;
    }

    /**
     * Remove slots
     *
     * @param \EL\CoreBundle\Entity\Slot $slots
     */
    public function removeSlot(\EL\CoreBundle\Entity\Slot $slots)
    {
        $this->slots->removeElement($slots);
    }

    /**
     * Get slots
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSlots()
    {
        return $this->slots;
    }

    /**
     * Set remake
     *
     * @param \EL\CoreBundle\Entity\Party $remake
     * @return Party
     */
    public function setRemake(\EL\CoreBundle\Entity\Party $remake = null)
    {
        $this->remake = $remake;
    
        return $this;
    }

    /**
     * Get remake
     *
     * @return \EL\CoreBundle\Entity\Party 
     */
    public function getRemake()
    {
        return $this->remake;
    }
    
    /**
     * Return data to put in json
     * 
     * @return array
     */
    public function jsonSerialize()
    {
        $slots = array();
        
        foreach ($this->getSlots() as $slot) {
            $slots[$slot->getPosition()] = $slot->jsonSerialize();
        }
        
        return array(
            'id'                    => $this->getId(),
            'slug'                  => $this->getSlug(),
            'disallowChat'         => $this->getDisallowChat(),
            'disallowObservers'    => $this->getDisallowObservers(),
            'title'                 => $this->getTitle(),
            'state'                 => $this->getState(),
            'private'               => $this->getPrivate(),
            'room'                  => $this->getRoom(),
            'host'                  => is_null($this->getHost()) ? null : $this->getHost()->jsonSerialize(),
            'game'                  => $this->getGame()->jsonSerialize(),
            'slots'                 => $slots,
            'dateCreate'           => $this->getDateCreate(),
            'dateStarted'          => $this->getDateStarted(),
            'dateEnded'            => $this->getDateEnded(),
        );
    }
    
    /**
     * create a remake party from this
     * 
     * @return Party
     */
    public function createRemake()
    {
        $clone = new self();
        
        $clone->game                = $this->game;
        $clone->title               = $this->title;
        $clone->private             = $this->private;
        $clone->room                = $this->room;
        $clone->disallowChat       = $this->disallowChat;
        $clone->disallowObservers  = $this->disallowObservers;
        $clone->state               = self::PREPARATION;
        $clone->dateCreate         = new \DateTime();
        
        return $clone;
    }
}
