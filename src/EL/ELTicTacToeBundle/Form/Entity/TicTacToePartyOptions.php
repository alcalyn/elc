<?php

namespace EL\ELTicTacToeBundle\Form\Entity;

/**
 *
 */
class TicTacToePartyOptions
{
    /**
     * @var integer
     * 
     * First player to play :
     * 0: random (default)
     * 1: first player
     * 2: second player
     */
    private $first_player;
    
    
    public function __construct()
    {
        $this->setFirstPlayer(0);
    }
    
    
    public function getFirstPlayer()
    {
        return $this->first_player;
    }

    public function setFirstPlayer($first_player)
    {
        $this->first_player = $first_player;
        return $this;
    }
}
