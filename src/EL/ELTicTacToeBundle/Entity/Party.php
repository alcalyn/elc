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
    const RANDOM_PLAYER         = 0;
    const PLAYER_X              = 1;
    const PLAYER_O              = 2;
    
    const END_ON_PARTIES_NUMBER = 1;
    const END_ON_WINS_NUMBER    = 2;
    const END_ON_DRAWS_NUMBER   = 3;
    
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
     * @var integer
     * 
     * Number of wins or party before end
     *
     * @ORM\Column(name="number_of_parties", type="smallint")
     */
    private $numberOfParties;

    /**
     * @var integer
     * 
     * Number of wins or party before end
     *
     * @ORM\Column(name="victory_condition", type="smallint")
     */
    private $victoryCondition;
    
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
    
    /**
     * Datetime for the last party end.
     * Used for wait a few second before restart a new grid,
     * and show to opponent the last ticked case
     *
     * @var \DateTime
     * 
     * @ORM\Column(name="last_party_end", type="datetime", nullable=true)
     */
    private $last_party_end;
    
    
    public function __construct()
    {
        $this
            ->setFirstPlayer(self::RANDOM_PLAYER)
            ->setVictoryCondition(self::END_ON_PARTIES_NUMBER)
            ->setNumberOfParties(2)
            ->setGrid('---------')
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
            'id'                => $this->getId(),
            'first_player'      => $this->getFirstPlayer(),
            'current_player'    => $this->getCurrentPlayer(),
            'grid'              => $this->getGrid(),
        );
    }
    
    
    public function createClone($clone_core_party)
    {
        $clone = new self();
        
        $clone->party           = $clone_core_party;
        $clone->firstPlayer     = $this->firstPlayer;
        $clone->current_player  = $this->firstPlayer == 0 ? rand(1, 2) : $this->firstPlayer ;
        $clone->grid            = '---------';
        
        return $clone;
    }

    /**
     * Set last_party_end
     *
     * @param \DateTime $lastPartyEnd
     * @return Party
     */
    public function setLastPartyEnd($lastPartyEnd)
    {
        $this->last_party_end = $lastPartyEnd;
    
        return $this;
    }

    /**
     * Get last_party_end
     *
     * @return \DateTime 
     */
    public function getLastPartyEnd()
    {
        return $this->last_party_end;
    }

    /**
     * Set numberOfParties
     *
     * @param integer $numberOfParties
     * @return Party
     */
    public function setNumberOfParties($numberOfParties)
    {
        $this->numberOfParties = $numberOfParties;
    
        return $this;
    }

    /**
     * Get numberOfParties
     *
     * @return integer 
     */
    public function getNumberOfParties()
    {
        return $this->numberOfParties;
    }

    /**
     * Set victoryCondition
     *
     * @param integer $victoryCondition
     * @return Party
     */
    public function setVictoryCondition($victoryCondition)
    {
        $this->victoryCondition = $victoryCondition;
    
        return $this;
    }

    /**
     * Get victoryCondition
     *
     * @return integer 
     */
    public function getVictoryCondition()
    {
        return $this->victoryCondition;
    }
}