<?php

namespace EL\ELCoreBundle\Services;

use EL\ELCoreBundle\Entity\Player;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class SessionService
{
    const PSEUDO_UNAVAILABLE = -1;
    const ALREADY_LOGGED = -2;
    
    
    private $security_context;
    private $em;
    private $elcore_security;
    private $illflushitlater;
    
    
    public function __construct($security_context, $em, $elcore_security, $illflushitlater)
    {
        $this->security_context = $security_context;
        $this->em = $em;
        $this->elcore_security = $elcore_security;
        $this->illflushitlater = $illflushitlater;
        
        $this->start();
    }
    
    public function start()
    {
        if (is_null($this->getPlayer())) {
            $guest = self::generateGuest();
            $this->setPlayer($guest);
            $this->savePlayer();
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
        
        $password_hash = $this
                ->elcore_security
                ->encodePassword($password, $player->getSalt());
        
        $player
                ->setPseudo($pseudo)
                ->setPasswordHash($password_hash)
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
        return !$this->getPlayer()->getInvited();
    }
    
    public function getPlayer()
    {
    	if (!is_object($this->security_context->getToken())) {
    		return null;
    	}
        $user = $this->security_context->getToken()->getUser();
        return ($user instanceof Player) ? $user : null ;
    }
    
    public function setPlayer($player)
    {
        $token = new UsernamePasswordToken(
                $player,
                $player->getPassword(),
                'main',
                $player->getRoles()
        );
        $this->security_context->setToken($token);
        return $this;
    }
    
    public function savePlayer()
    {
        $this->illflushitlater->merge($this->getPlayer());
        $this->illflushitlater->flush();
        return $this;
    }
    
    public function generateGuest($lang = 'en') {
        $guest = new Player();
        
        return $guest
                ->setPseudo(self::generateGuestName($lang))
                ->setInvited(true);
    }
    
    public function generateGuestName($lang = 'en')
    {
        return 'Guest '.rand(10000, 99999);
    }
    
    
}
