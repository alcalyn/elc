<?php

namespace EL\ELCoreBundle\Services;

use EL\ELCoreBundle\Entity\Game;


class CoreGameService
{
    
    private $game = null;
    
    
    public function setGame(Game $game)
    {
        $this->game = $game;
        return $this;
    }
    
    
    
    
    public function getGameServiceName()
    {
        return 'el_games.'.$this->game->getName();
    }
}