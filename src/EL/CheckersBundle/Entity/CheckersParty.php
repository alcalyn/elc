<?php

namespace EL\CheckersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EL\CoreBundle\Entity\Party;

/**
 * CheckersParty
 *
 * @ORM\Table(name="el_games_checkers_party")
 * @ORM\Entity
 */
class CheckersParty implements \JsonSerializable
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
     * @var Party
     * 
     * @ORM\OneToOne(targetEntity="EL\CoreBundle\Entity\Party")
     */
    private $party;

    /**
     * Variant parameters
     * 
     * @var integer
     *
     * @ORM\Column(name="parameters", type="integer")
     */
    private $parameters;
    
    /**
     * Json serialized array containing pieces
     * 
     * @var array
     *
     * @ORM\Column(name="grid", type="json_array", nullable=true)
     */
    private $grid;

    /**
     * Current player, Checkers::WHITE or Checkers::BLACK
     * 
     * @var boolean
     *
     * @ORM\Column(name="currentPlayer", type="boolean")
     */
    private $currentPlayer;

    /**
     * Last move
     * 
     * @var string
     *
     * @ORM\Column(name="lastMove", type="string", length=255, nullable=true)
     */
    private $lastMove;

    /**
     * For captures rules, best moves are stored here
     * to check if user follows one of them
     * 
     * @var string
     *
     * @ORM\Column(name="bestMultipleCaptures", type="string", length=1023, nullable=true)
     */
    private $bestMultipleCaptures;
    
    /**
     * Data about huff
     * 
     * @var string
     *
     * @ORM\Column(name="huff", type="string", length=256, nullable=true)
     */
    private $huff;

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
     * Set parameters
     *
     * @param integer $parameters
     * @return CheckersParty
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    
        return $this;
    }

    /**
     * Get parameters
     *
     * @return integer 
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set grid
     *
     * @param array $grid
     * @return CheckersParty
     */
    public function setGrid(array $grid)
    {
        $this->grid = $grid;
    
        return $this;
    }

    /**
     * Get grid
     *
     * @return array 
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * Set currentPlayer
     *
     * @param boolean $currentPlayer
     * @return CheckersParty
     */
    public function setCurrentPlayer($currentPlayer)
    {
        $this->currentPlayer = $currentPlayer;
    
        return $this;
    }

    /**
     * Get currentPlayer
     *
     * @return boolean 
     */
    public function getCurrentPlayer()
    {
        return $this->currentPlayer;
    }

    /**
     * Change current player
     *
     * @return \EL\CheckersBundle\Entity\CheckersParty
     */
    public function changeCurrentPlayer()
    {
        $this->currentPlayer = !$this->currentPlayer;
        
        return $this;
    }

    /**
     * Set lastMove
     *
     * @param string $lastMove
     * @return CheckersParty
     */
    public function setLastMove($lastMove)
    {
        $this->lastMove = $lastMove;
    
        return $this;
    }

    /**
     * Get lastMove
     *
     * @return string 
     */
    public function getLastMove()
    {
        return $this->lastMove;
    }

    /**
     * Set party
     *
     * @param \EL\CoreBundle\Entity\Party $party
     * @return CheckersParty
     */
    public function setParty(\EL\CoreBundle\Entity\Party $party = null)
    {
        $this->party = $party;
    
        return $this;
    }

    /**
     * Get party
     *
     * @return \EL\CoreBundle\Entity\Party 
     */
    public function getParty()
    {
        return $this->party;
    }

    /**
     * Set bestMultipleCaptures
     *
     * @param string $bestMultipleCaptures
     * @return CheckersParty
     */
    public function setBestMultipleCaptures($bestMultipleCaptures)
    {
        $this->bestMultipleCaptures = $bestMultipleCaptures;
    
        return $this;
    }

    /**
     * Get bestMultipleCaptures
     *
     * @return string 
     */
    public function getBestMultipleCaptures()
    {
        return $this->bestMultipleCaptures;
    }

    /**
     * Set huff
     *
     * @param string $huff
     * @return CheckersParty
     */
    public function setHuff($huff)
    {
        $this->huff = $huff;
    
        return $this;
    }

    /**
     * Get huff
     *
     * @return string 
     */
    public function getHuff()
    {
        return $this->huff;
    }
    
    /**
     * Implements JsonSerializable
     * 
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'id'            => $this->getId(),
            'currentPlayer' => $this->getCurrentPlayer(),
            'lastMove'      => json_decode($this->getLastMove()),
            'parameters'    => $this->getParameters(),
            'grid'          => $this->getGrid(),
            'huff'          => json_decode($this->getHuff()),
        );
    }
}