<?php

namespace EL\ELCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EL\ELCoreBundle\Entity\Slot;

/**
 * Party
 *
 * @ORM\Table(name="el_core_party")
 * @ORM\Entity(repositoryClass="EL\ELCoreBundle\Repository\PartyRepository")
 */
class Party
{
    
    const PREPARATION   = 1;
    const ACTIVE        = 2;
    const ENDED         = 3;
    
    
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
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Game")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;
    
    /**
     * @var Player
     * 
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Player")
     * @ORM\JoinColumn(nullable=true)
     */
    private $host;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="EL\ELCoreBundle\Entity\Slot", mappedBy="party")
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
     * @var boolean
     *
     * @ORM\Column(name="open", type="boolean")
     */
    private $open;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="smallint")
     */
    private $state;
    
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
     * @ORM\Column(name="allow_chat", type="boolean")
     */
    private $allow_chat;
    
    /**
     * @var boolean
     * 
     * If this party allows observers
     * 
     * @ORM\Column(name="allow_observers", type="boolean")
     */
    private $allow_observers;
    
    
    
    public function __construct()
    {
        $this
                ->setOpen(true)
                ->setState(self::PREPARATION)
                ->setRoom(true)
                ->setAllowChat(true)
                ->setAllowObservers(true)
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
     * Set open
     *
     * @param boolean $open
     * @return Party
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
     * Set game
     *
     * @param \stdClass $game
     * @return Party
     */
    public function setGame($game)
    {
        $this->game = $game;
    
        return $this;
    }

    /**
     * Get game
     *
     * @return \stdClass 
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set host
     *
     * @param \EL\ELCoreBundle\Entity\Player $host
     * @return Party
     */
    public function setHost(\EL\ELCoreBundle\Entity\Player $host = null)
    {
        $this->host = $host;
    
        return $this;
    }

    /**
     * Get host
     *
     * @return \EL\ELCoreBundle\Entity\Player 
     */
    public function getHost()
    {
        return $this->host;
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
     * Add slots
     *
     * @param \EL\ELCoreBundle\Entity\Slot $slots
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
     * @param \EL\ELCoreBundle\Entity\Slot $slots
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
     * Get slot at index $index
     * 
     * @param integer $index
     * @return \EL\ELCoreBundle\Entity\Slot
     */
    public function getSlot($index)
    {
        return $this->slots[$index];
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
    
    
    public function jsonSerialize() {
        return array(
            'host'      => $this->getHost()->jsonSerialize(),
            'title'     => $this->getTitle(),
            'state'     => $this->getState(),
        );
    }


    /**
     * Set allow_chat
     *
     * @param boolean $allowChat
     * @return Party
     */
    public function setAllowChat($allowChat)
    {
        $this->allow_chat = $allowChat;
    
        return $this;
    }

    /**
     * Get allow_chat
     *
     * @return boolean 
     */
    public function getAllowChat()
    {
        return $this->allow_chat;
    }

    /**
     * Set allow_observers
     *
     * @param boolean $allowObservers
     * @return Party
     */
    public function setAllowObservers($allowObservers)
    {
        $this->allow_observers = $allowObservers;
    
        return $this;
    }

    /**
     * Get allow_observers
     *
     * @return boolean 
     */
    public function getAllowObservers()
    {
        return $this->allow_observers;
    }
}