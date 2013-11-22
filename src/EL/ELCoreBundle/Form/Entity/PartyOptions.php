<?php

namespace EL\ELCoreBundle\Form\Entity;

use Symfony\Component\Validator\Constraints as Assert;

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
	
	
}
