<?php

namespace EL\ELCoreBundle\Form\Entity;

use EL\ELCoreBundle\Form\Entity\SpecialPartyOptions;

class PartyOptions
{
    /**
     * Title of the party visible by other players
     * 
     * @var string
     */
    private $title;
    
    /**
     * Private party, you cant join unless invitation
     * 
     * @var boolean
     */
    private $private;
    
    /**
     * Disallow others players to observe this party when playing
     * 
     * @var boolean
     */
    private $disallow_observers;
    
    /**
     * Disable chat for this party
     * 
     * @var boolean
     */
    private $disallow_chat;
    
    
    
    /**
     * Special party options
     * 
     * @var SpecialPartyOptions
     */
    private $special_party_options;
    
    
    
    public function __construct()
    {
        $this
                ->setPrivate(false)
                ->setDisallowObservers(false)
                ->setDisallowChat(false)
        ;
    }
    
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
    public function getPrivate()
    {
        return $this->private;
    }

    public function setPrivate($private)
    {
        $this->private = $private;
        return $this;
    }
    
    public function getDisallowObservers()
    {
        return $this->disallow_observers;
    }

    public function setDisallowObservers($disallow_observers)
    {
        $this->disallow_observers = $disallow_observers;
        return $this;
    }

    public function getDisallowChat()
    {
        return $this->disallow_chat;
    }
    
    public function setDisallowChat($disallow_chat)
    {
        $this->disallow_chat = $disallow_chat;
        return $this;
    }

    public function getSpecialPartyOptions()
    {
        return $this->special_party_options;
    }

    public function setSpecialPartyOptions($special_party_options)
    {
        $this->special_party_options = $special_party_options;
        return $this;
    }
}
