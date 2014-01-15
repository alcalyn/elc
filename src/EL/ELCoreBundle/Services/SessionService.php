<?php

namespace EL\ELCoreBundle\Services;

use EL\ELCoreBundle\Entity\Player;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;


class SessionService
{
    const PSEUDO_UNAVAILABLE = -1;
    const ALREADY_LOGGED = -2;
    
    
    private $security_context;
    private $em;
    private $elcore_security;
    private $illflushitlater;
    private $request;
    private $event_dispatcher;
    
    
    public function __construct($security_context, $em, $elcore_security, $illflushitlater, $request, $event_dispatcher)
    {
        $this->security_context = $security_context;
        $this->em = $em;
        $this->elcore_security = $elcore_security;
        $this->illflushitlater = $illflushitlater;
        $this->request = $request;
        $this->event_dispatcher = $event_dispatcher;
        
        $this->start();
    }
    
    public function start()
    {
        if (is_null($this->getPlayer())) {
            $guest = self::generateGuest();
            $this->logPlayer($guest);
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
        return $this->security_context->getToken()->getUser();
    }
    
    public function logPlayer($player)
    {
        $token = new UsernamePasswordToken(
                $player,
                $player->getPassword(),
                'main',
                $player->getRoles()
        );
        
        $this->security_context->setToken($token);
        
        $event = new InteractiveLoginEvent($this->request, $token);
        $this->event_dispatcher->dispatch('security.interactive_login', $event);
        
        return $this;
    }
    
    public function savePlayer()
    {
        $this->illflushitlater->merge($this->getPlayer());
        $this->illflushitlater->flush();
        return $this;
    }
    
    public static function generateGuest($lang = 'en')
    {
        $guest = new Player();
        
        return $guest
                ->setPseudo(self::generateGuestName($lang))
                ->setInvited(true)
        ;
    }
    
    public static function generateGuestName($lang = 'en')
    {
        return 'Guest '.rand(10000, 99999);
    }
    
    
}
