<?php

namespace EL\AwaleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EL\CoreBundle\Entity\Party;

/**
 * AwaleParty
 *
 * @ORM\Table(name="el_games_awale_party")
 * @ORM\Entity(repositoryClass="EL\AwaleBundle\Repository\AwalePartyRepository")
 */
class AwaleParty implements \JsonSerializable
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
     * @var integer
     *
     * @ORM\Column(name="seeds_per_container", type="smallint")
     */
    private $seedsPerContainer;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="first_player", type="smallint")
     */
    private $firstPlayer;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="current_player", type="smallint")
     */
    private $currentPlayer;
    
    /**
     * @var string
     *
     * @ORM\Column(name="grid", type="string", length=63)
     */
    private $grid;
    
    /**
     * @var string
     * 
     * Contains last move under the form:
     * [Move number]|[container moved 0..5]
     * 
     * @ORM\Column(name="last_move", type="string", length=7)
     */
    private $lastMove;
    
    
    public function __construct()
    {
        $this
                ->setFirstPlayer(rand(0, 1))
                ->setCurrentPlayer($this->getFirstPlayer())
                ->setLastMove('0')
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
     * Set firstPlayer
     *
     * @param integer $firstPlayer
     * @return AwaleParty
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
     * @param \EL\CoreBundle\Entity\Party $party
     * @return AwaleParty
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
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'id'                => $this->getId(),
            'seedsPerContainer' => $this->getSeedsPerContainer(),
            'firstPlayer'       => $this->getFirstPlayer(),
            'currentPlayer'     => $this->getCurrentPlayer(),
            'grid'              => $this->getGrid(),
            'lastMove'          => $this->getLastMove(),
        );
    }
}
