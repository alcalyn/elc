<?php

namespace EL\ELTicTacToeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TicTacToeParty
 *
 * @ORM\Table(name="el_games_tictactoe_party")
 * @ORM\Entity(repositoryClass="EL\ELTicTacToeBundle\Repository\PartyRepository")
 */
class Party implements \JsonSerializable
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
     *
     * @var \EL\ELCoreBundle\Entity\Party 
     * 
     * @ORM\OneToOne(targetEntity="EL\ELCoreBundle\Entity\Party")
     */
    private $party;

    /**
     * @var integer
     * 
     * 0: random ; 1: first player ; 2: second player
     *
     * @ORM\Column(name="first_player", type="smallint")
     */
    private $firstPlayer;
    
    /**
     * @var string
     * 
     * Represent the tic tac toe grid
     * -OX
     * Example: -O--XO-XX
     *
     * @ORM\Column(name="grid", type="string", length=9, nullable=true, options={"default" = "---------"})
     */
    private $grid;
    
    /**
     * @var integer
     * 
     * Current player 1 or 2
     *
     * @ORM\Column(name="current_player", type="smallint", nullable=true)
     */
    private $current_player;
    
    
    
    public function __construct()
    {
    	$this->setGrid('---------');
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
     * Set firstPlayer
     *
     * @param integer $firstPlayer
     * @return TicTacToeParty
     */
    public function setFirstPlayer($firstPlayer)
    {
        $this->firstPlayer = $firstPlayer;
    
        return $this;
    }

    /**
     * Get firstPlayer
     *
     * @return integer 
     */
    public function getFirstPlayer()
    {
        return $this->firstPlayer;
    }

    /**
     * Set party
     *
     * @param \EL\ELCoreBundle\Entity\Party $party
     * @return Party
     */
    public function setParty(\EL\ELCoreBundle\Entity\Party $party = null)
    {
        $this->party = $party;
    
        return $this;
    }

    /**
     * Get party
     *
     * @return \EL\ELCoreBundle\Entity\Party 
     */
    public function getParty()
    {
        return $this->party;
    }

    /**
     * Set grid
     *
     * @param string $grid
     * @return Party
     */
    public function setGrid($grid)
    {
        $this->grid = $grid;
    
        return $this;
    }

    /**
     * Get grid
     *
     * @return string 
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * Set current_player
     *
     * @param integer $currentPlayer
     * @return Party
     */
    public function setCurrentPlayer($currentPlayer)
    {
        $this->current_player = $currentPlayer;
    
        return $this;
    }

    /**
     * Get current_player
     *
     * @return integer 
     */
    public function getCurrentPlayer()
    {
        return $this->current_player;
    }
    
    
    public function jsonSerialize()
    {
    	return array(
    		'id'				=> $this->getId(),
    		'first_player'		=> $this->getFirstPlayer(),
    		'current_player'	=> $this->getCurrentPlayer(),
    		'grid'				=> $this->getGrid(),
    	);
    }
    
    
    public function createClone($clone_core_party)
    {
    	$clone = new self();
    	
    	$clone->party			= $clone_core_party;
    	$clone->firstPlayer		= $this->firstPlayer;
    	$clone->current_player	= $this->firstPlayer == 0 ? rand(1, 2) : $this->firstPlayer ;
    	$clone->grid			= '---------';
    	
    	return $clone;
    }
    
    
}