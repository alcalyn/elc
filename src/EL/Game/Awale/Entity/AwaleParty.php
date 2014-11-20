<?php

namespace EL\Game\Awale\Entity;

use Doctrine\ORM\Mapping as ORM;
use EL\Core\Entity\Party;

/**
 * AwaleParty
 */
class AwaleParty implements \JsonSerializable
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
     * @var integer
     */
    private $seedsPerContainer;
    
    /**
     * @var integer
     */
    private $currentPlayer;
    
    /**
     * @var string
     */
    private $grid;
    
    /**
     * @var string
     * 
     * Contains last move under the form:
     * [Move number]|[container moved 0..5]
     * such as "2|1" (move 2, player 2%2 moved his box 1)
     */
    private $lastMove;
    
    public function __construct()
    {
        $this
                ->setCurrentPlayer(0)
                ->setLastMove('0|0')
                ->setSeedsPerContainer(4)
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
     * Set seedsPerContainer
     *
     * @param integer $seedsPerContainer
     * @return AwaleParty
     */
    public function setSeedsPerContainer($seedsPerContainer)
    {
        $this->seedsPerContainer = $seedsPerContainer;
    
        return $this;
    }

    /**
     * Get seedsPerContainer
     *
     * @return integer
     */
    public function getSeedsPerContainer()
    {
        return $this->seedsPerContainer;
    }

    /**
     * Set currentPlayer
     *
     * @param integer $currentPlayer
     * @return AwaleParty
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
     * Set grid
     *
     * @param string $grid
     * @return AwaleParty
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
     * Set lastMove
     *
     * @param string $lastMove
     * @return AwaleParty
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
     * @return AwaleParty
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
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'id'                => $this->getId(),
            'seedsPerContainer' => $this->getSeedsPerContainer(),
            'currentPlayer'     => $this->getCurrentPlayer(),
            'grid'              => $this->getGrid(),
            'lastMove'          => $this->getLastMove(),
        );
    }
}
