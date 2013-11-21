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
    
    
    public function __construct($security_context, $em)
    {
        $this->security_context = $security_context;
        $this->em = $em;
        $this->start();
    }
    
    public function start()
    {
        if (is_null($this->getPlayer())) {
            $guest = self::generateGuest();
            $this->setPlayer($guest);
        }
    }
    
    public function logout()
    {
        if ($this->getPlayer()->getInvited()) {
            
        } else {
            
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
                ->setPasswordHash($this->encodePassword($password, $player->getSalt()))
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
        $player = $this->getPlayer();
        $newplayer = $this->em->merge($player);
        //$this->setPlayer($newplayer);
        $this->em->flush();
        return $this;
    }
    
    
    public static function generateGuest($lang = 'en') {
        $guest = new Player();
        
        return $guest
                ->setPseudo(self::generateGuestName($lang))
                ->setInvited(true);
    }
    
    public static function generateGuestName($lang = 'en')
    {
        return 'Guest '.rand(10000, 99999);
    }
    
    
}
