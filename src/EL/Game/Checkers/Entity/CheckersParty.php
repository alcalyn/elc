<?php

namespace EL\Game\Checkers\Entity;

use Doctrine\ORM\Mapping as ORM;
use EL\Core\Entity\Party;

/**
 * CheckersParty
 */
class CheckersParty implements \JsonSerializable
{
    /**
     * @var integer
     */
    private $id;
    
    /**
     * @var Party
     */
    private $party;

    /**
     * Variant parameters
     * 
     * @var integer
     */
    private $parameters;
    
    /**
     * Json serialized array containing pieces
     * 
     * @var array
     */
    private $grid;

    /**
     * Current player, Checkers::WHITE or Checkers::BLACK
     * 
     * @var boolean
     */
    private $currentPlayer;

    /**
     * Last move
     * 
     * @var string
     */
    private $lastMove;

    /**
     * For captures rules, best moves are stored here
     * to check if user follows one of them
     * 
     * @var string
     */
    private $bestMultipleCaptures;
    
    /**
     * Data about huff
     * 
     * @var string
     */
    private $huff;
    
    /**
     * Incremented each turn where king moved and no captures done,
     * draw party when it reaches 25
     * 
     * @var integer
     */
    private $drawNoMove;
    
    /**
     * Incremented at 1 vs 3 pieces, a king each,
     * draw party when it reaches 16
     * 
     * @var integer
     */
    private $drawNotEnough;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this
                ->setDrawNoMove(0)
                ->setDrawNotEnough(0)
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
     * @return \EL\Game\Checkers\Entity\CheckersParty
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
     * @param \EL\Core\Entity\Party $party
     * @return CheckersParty
     */
    public function setParty(\EL\Core\Entity\Party $party = null)
    {
        $this->party = $party;
    
        return $this;
    }

    /**
     * Get party
     *
     * @return \EL\Core\Entity\Party
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

    /**
     * Set drawNoMove
     *
     * @param integer $drawNoMove
     * @return CheckersParty
     */
    public function setDrawNoMove($drawNoMove)
    {
        $this->drawNoMove = $drawNoMove;
    
        return $this;
    }

    /**
     * Get drawNoMove
     *
     * @return integer
     */
    public function getDrawNoMove()
    {
        return $this->drawNoMove;
    }

    /**
     * Set drawNotEnough
     *
     * @param integer $drawNotEnough
     * @return CheckersParty
     */
    public function setDrawNotEnough($drawNotEnough)
    {
        $this->drawNotEnough = $drawNotEnough;
    
        return $this;
    }

    /**
     * Get drawNotEnough
     *
     * @return integer
     */
    public function getDrawNotEnough()
    {
        return $this->drawNotEnough;
    }
}
