<?php

namespace EL\Core\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use EL\Core\Entity\Slot;

/**
 * Party
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
     */
    private $id;
    
    /**
     * @var Game
     */
    private $game;
    
    /**
     * @var Player
     */
    private $host;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $slots;

    /**
     * @var string
     * 
     * @Assert\NotBlank()
     */
    private $title;
    
    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"})

     */
    private $slug;

    /**
     * @var integer
     */
    private $state;

    /**
     * @var boolean
     */
    private $private;
    
    /**
     * @var boolean
     * 
     * If the game is room mode,
     * set this parameter to false
     * to disable room mode on this party
     */
    private $room;
    
    /**
     * @var boolean
     * 
     * If the chat is enabled for this party
     */
    private $disallowChat;
    
    /**
     * @var boolean
     * 
     * If this party allows observers
     */
    private $disallowObservers;
    
    /**
     * Contains party which is the clone of this
     * created by the player who has remade an older party
     */
    private $remake;
    
    /**
     * @var \DateTime
     */
    private $dateCreate;
    
    /**
     * @var \DateTime
     */
    private $dateStarted;
    
    /**
     * @var \DateTime
     */
    private $dateEnded;
    
    
    /**
     * Constructor
     */
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
     * @param Game $game
     * @return Party
     */
    public function setGame(Game $game)
    {
        $this->game = $game;
    
        return $this;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set host
     *
     * @param Player $host
     * @return Party
     */
    public function setHost(Player $host = null)
    {
        $this->host = $host;
    
        return $this;
    }

    /**
     * Get host
     *
     * @return Player
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Add slots
     *
     * @param Slot $slots
     * @return Party
     */
    public function addSlot(Slot $slots)
    {
        $this->slots[] = $slots;
    
        return $this;
    }

    /**
     * Remove slots
     *
     * @param Slot $slots
     */
    public function removeSlot(Slot $slots)
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
     * @param Party $remake
     * @return Party
     */
    public function setRemake(Party $remake = null)
    {
        $this->remake = $remake;
    
        return $this;
    }

    /**
     * Get remake
     *
     * @return Party
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
            'id'                => $this->getId(),
            'slug'              => $this->getSlug(),
            'disallowChat'      => $this->getDisallowChat(),
            'disallowObservers' => $this->getDisallowObservers(),
            'title'             => $this->getTitle(),
            'state'             => $this->getState(),
            'private'           => $this->getPrivate(),
            'room'              => $this->getRoom(),
            'host'              => is_null($this->getHost()) ? null : $this->getHost()->jsonSerialize(),
            'game'              => $this->getGame()->jsonSerialize(),
            'slots'             => $slots,
            'dateCreate'        => $this->getDateCreate(),
            'dateStarted'       => $this->getDateStarted(),
            'dateEnded'         => $this->getDateEnded(),
        );
    }
}
