<?php

namespace EL\ELCoreBundle\Form\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use EL\ELCoreBundle\Form\Entity\SpecialPartyOptions;

/**
 *
 */
class PartyOptions {
    
	
	/**
	 * Title of the party visible by other players
	 * 
	 * @var string
	 */
	private $title;
	
	/**
	 * Allow others players to access this party
	 * 
	 * @var boolean
	 */
	private $allow_observers;
	
	/**
	 * Private party, you cant join unless invitation
	 * 
	 * @var boolean
	 */
	private $private;
    
    
    /**
     * Special party options
     * 
     * @var SpecialPartyOptions
     */
    private $special_party_options;
	
	
    
    public function __construct()
    {
        $this->allow_observers = true;
        $this->private = false;
    }
    
    public function getTitle()
    {
        return $this->title;
    }

    public function getAllowObservers()
    {
        return $this->allow_observers;
    }

    public function getPrivate()
    {
        return $this->private;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setAllowObservers($allow_observers)
    {
        $this->allow_observers = $allow_observers;
        return $this;
    }

    public function setPrivate($private)
    {
        $this->private = $private;
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
