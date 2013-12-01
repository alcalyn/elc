<?php

namespace EL\ELTicTacToeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TicTacToeParty
 *
 * @ORM\Table(name="el_games_tictactoe_party")
 * @ORM\Entity(repositoryClass="EL\ELTicTacToeBundle\Repository\PartyRepository")
 */
class Party
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
     * @ORM\Column(name="first_player", type="smallint")
     */
    private $firstPlayer;


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
}