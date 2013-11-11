<?php

namespace EL\ELCoreBundle\Services;

use EL\ELCoreBundle\Entity\Player;

class SessionService
{
    
    private $session;
    private $em;
    
    
    public function __construct($session, $em)
    {
        $this->session = $session;
        $this->em = $em;
        $this->start();
    }
    
    public function start()
    {
        $this->session->start();
        
        if ($this->session->has('player')) {
            return $this->session->get('player');
        } else {
            $guest = Player::generateGuest('en');
            $this->session->set('player', $guest);
            $this->em->persist($guest);
            $this->em->flush();
            return $guest;
        }
    }
    
    public function getPlayer()
    {
        return $this->session->get('player');
    }
    
    public function setPlayer($player)
    {
        $this->session->set('player', $player);
        return $this;
    }
    
}
