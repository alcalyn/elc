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
     * @ORM\Column(name="parameters", type="smallint")
     */
    private $parameters;

    /**
     * Current player, Checkers::WHITE or Checkers::BLACK
     * 
     * @var integer
     *
     * @ORM\Column(name="currentPlayer", type="boolean")
     */
    private $currentPlayer;

    /**
     * Last move
     * 
     * @var string
     *
     * @ORM\Column(name="lastMove", type="string", length=31, nullable=true)
     */
    private $lastMove;

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
     * Implements JsonSerializable
     * 
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'id'            => $this->getId(),
            'currentPlayer' => $this->getCurrentPlayer(),
            'lastMove'      => $this->getLastMove(),
            'parameters'    => $this->getParameters(),
        );
    }
}
