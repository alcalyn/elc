<?php

namespace EL\Bundle\Game\TicTacToeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TicTacToeParty
 *
 * @ORM\Table(name="el_games_tictactoe_party")
 * @ORM\Entity(repositoryClass="EL\Bundle\Game\TicTacToeBundle\Repository\TicTacToePartyRepository")
 */
class TicTacToeParty implements \JsonSerializable
{
    const PLAYER_X              = 0;
    const PLAYER_O              = 1;
    
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
     * @var \EL\Bundle\CoreBundle\Entity\Party 
     * 
     * @ORM\OneToOne(targetEntity="EL\Bundle\CoreBundle\Entity\Party")
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
    private $currentPlayer;
    
    /**
     * @var integer
     * 
     * Number of current party, used to count parties for END_ON_PARTIES_NUMBER condition
     *
     * @ORM\Column(name="party_number", type="smallint", nullable=true)
     */
    private $partyNumber;
    
    /**
     * Datetime for the last party end.
     * Used for wait a few second before restart a new grid,
     * and show to opponent the last ticked case
     *
     * @var \DateTime
     * 
     * @ORM\Column(name="last_party_end", type="datetime", nullable=true)
     */
    private $lastPartyEnd;
    
    
    public function __construct()
    {
        $this
            ->setFirstPlayer(rand(0, 1))
            ->setCurrentPlayer($this->getFirstPlayer())
            ->setVictoryCondition(self::END_ON_PARTIES_NUMBER)
            ->setNumberOfParties(2)
            ->setGrid('---------')
            ->setPartyNumber(1)
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
     * @param \EL\Bundle\CoreBundle\Entity\Party $party
     * @return TicTacToeParty
     */
    public function setParty(\EL\Bundle\CoreBundle\Entity\Party $party = null)
    {
        $this->party = $party;
    
        return $this;
    }

    /**
     * Get party
     *
     * @return \EL\Bundle\CoreBundle\Entity\Party 
     */
    public function getParty()
    {
        return $this->party;
    }

    /**
     * Set grid
     *
     * @param string $grid
     * @return TicTacToeParty
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
     * Set currentPlayer
     *
     * @param integer $currentPlayer
     * @return TicTacToeParty
     */
    public function setCurrentPlayer($currentPlayer)
    {
        $this->currentPlayer = $currentPlayer;
    
        return $this;
    }

    /**
     * Get currentPlayer
     *
     * @return integer 
     */
    public function getCurrentPlayer()
    {
        return $this->currentPlayer;
    }

    /**
     * Set lastPartyEnd
     *
     * @param \DateTime $lastPartyEnd
     * @return TicTacToeParty
     */
    public function setLastPartyEnd($lastPartyEnd)
    {
        $this->lastPartyEnd = $lastPartyEnd;
    
        return $this;
    }

    /**
     * Get lastPartyEnd
     *
     * @return \DateTime 
     */
    public function getLastPartyEnd()
    {
        return $this->lastPartyEnd;
    }

    /**
     * Set numberOfParties
     *
     * @param integer $numberOfParties
     * @return TicTacToeParty
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
     * @return TicTacToeParty
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

    /**
     * Set partyNumber
     *
     * @param integer $partyNumber
     * @return TicTacToeParty
     */
    public function setPartyNumber($partyNumber)
    {
        $this->partyNumber = $partyNumber;
    
        return $this;
    }

    /**
     * Get partyNumber
     *
     * @return integer 
     */
    public function getPartyNumber()
    {
        return $this->partyNumber;
    }
    
    /**
     * Json serialize implementation
     * 
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'id'                => $this->getId(),
            'coreParty'         => $this->getParty()->jsonSerialize(),
            'firstPlayer'       => $this->getFirstPlayer(),
            'currentPlayer'     => $this->getCurrentPlayer(),
            'partyNumber'       => $this->getPartyNumber(),
            'grid'              => $this->getGrid(),
        );
    }
}
