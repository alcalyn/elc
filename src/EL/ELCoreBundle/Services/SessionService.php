<?php

namespace EL\ELCoreBundle\Services;

use EL\ELCoreBundle\Entity\Player;

class SessionService
{
    const PSEUDO_UNAVAILABLE = -1;
    const ALREADY_LOGGED = -2;
    
    
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
            $this->savePlayer();
            return $guest;
        }
    }
    
    public function login($pseudo, $password)
    {
        $results = $this->em
                ->getRepository('ELCoreBundle:Player')
                ->loginQuery($pseudo, $password);
        
        if (count($results) == 1) {
            $this->setPlayer($results[0]);
            return 0;
        } else {
            // login error
            return -1;
        }
    }
    
    public function logout()
    {
        if ($this->getPlayer()->getInvited()) {
            
        } else {
            $this->session->invalidate();
            $this->session->start();
            return $this;
        }
    }
    
    public function signup($pseudo, $password)
    {
        if ($this->isLogged()) {
            return self::ALREADY_LOGGED;
        }
        
        if ($this->pseudoExists($pseudo)) {
            return self::PSEUDO_UNAVAILABLE;
        }
        
        $player = $this->getPlayer();
        $player
                ->setPseudo($pseudo)
                ->setPasswordHash(Player::hashPassword($password))
                ->setInvited(false);
        
        $this->savePlayer();
        
        return 0;
    }
    
    public function pseudoExists($pseudo)
    {
        $count = $this->em
                ->getRepository('ELCoreBundle:Player')
                ->pseudoCount($pseudo);
        
        return intval($count) > 0;
    }
    
    public function isLogged()
    {
        return !$this->session->get('player')->getInvited();
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
    
    public function savePlayer()
    {
        $this->em->persist($this->session->get('player'));
        $this->em->flush();
    }
    
}
