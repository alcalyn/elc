<?php

namespace EL\AwaleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EL\CoreBundle\Entity\Party;

/**
 * AwaleParty
 *
 * @ORM\Table(name="el_games_awale")
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
     * @ORM\Column(name="seeds_per_Container", type="smallint")
     */
    private $seedsPerContainer;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="current_player", type="smallint")
     */
    private $currentPlayer;
    
    /**
     * @var string
     *
     * @ORM\Column(name="grid", type="string", length=255)
     */
    private $grid;
    
    public function __construct()
    {
        $this
                ->setCurrentPlayer(0)
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
     * Set party
     *
     * @param Party $party
     * @return AwaleParty
     */
    public function setParty(Party $party = null)
    {
        $this->party = $party;
    
        return $this;
    }

    /**
     * Get party
     *
     * @return Party 
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
        );
    }
}
