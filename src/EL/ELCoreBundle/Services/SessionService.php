<?php

namespace EL\ELCoreBundle\Services;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use EL\ELCoreBundle\Model\ELUserException;
use EL\ELCoreBundle\Entity\Player;

class SessionService
{
    const PSEUDO_UNAVAILABLE = -1;
    const ALREADY_LOGGED = -2;
    
    
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;
    
    /**
     * 
     * @var IllFlushItLaterService
     */
    private $illflushitlater;
    
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    
    
    
    public function __construct(Session $session, IllFlushItLaterService $illflushitlater, EntityManager $em)
    {
        $this->session          = $session;
        $this->illflushitlater  = $illflushitlater;
        $this->em               = $em;
        
        $this->start();
    }
    
    /**
     * Init session, create a guest if first connexion
     * 
     * @return Player logged
     */
    public function start()
    {
        $this->session->start();
        
        if ($this->session->has('player')) {
            $this->savePlayer();
            return $this->getPlayer();
        } else {
            $guest = self::generateGuest('en');
            $this->setPlayer($guest);
            $this->savePlayer();
            return $guest;
        }
    }
    
    /**
     * Log an user
     * 
     * @param string $pseudo
     * @param string $password plain
     */
    public function login($pseudo, $password)
    {
        $passwordHash = $this->hashPassword($password);
        
        $results = $this->em
                ->getRepository('ELCoreBundle:Player')
                ->loginQuery($pseudo, $passwordHash);
        
        if (count($results) == 1) {
            $this->setPlayer($results[0]);
            
            return true;
        } else {
            throw new ELUserException('login.error');
        }
    }
    
    /**
     * Logout
     * 
     * @return \EL\ELCoreBundle\Services\SessionService
     */
    public function logout()
    {
        if ($this->getPlayer()->getInvited()) {
            
        } else {
            $this->session->invalidate();
            $this->session->start();
        }
        
        return $this;
    }
    
    /**
     * Create an account
     * 
     * @param type $pseudo
     * @param type $password
     * 
     * @return int
     */
    public function signup($pseudo, $password)
    {
        if ($this->isLogged()) {
            throw new ELCoreException('already.logged');
        }
        
        if ($this->pseudoExists($pseudo)) {
            throw new ELUserException('pseudo.unavailable');
        }
        
        $player = $this->getPlayer();
        $player
                ->setPseudo($pseudo)
                ->setPasswordHash($this->hashPassword($password))
                ->setInvited(false);
        
        $this->savePlayer();
        
        return 0;
    }
    
    /**
     * Check if pseudo exists
     * 
     * @param string $pseudo
     * @return boolean
     */
    public function pseudoExists($pseudo)
    {
        $count = $this->em
                ->getRepository('ELCoreBundle:Player')
                ->pseudoCount($pseudo);
        
        return intval($count) > 0;
    }
    
    /**
     * Return false if there is an invited user
     * 
     * @return boolean
     */
    public function isLogged()
    {
        return !$this->getPlayer()->getInvited();
    }
    
    /**
     * Return logged player
     * 
     * @return Player
     */
    public function getPlayer()
    {
        return $this->session->get('player');
    }
    
    /**
     * Set player
     * 
     * @param Player $player
     * 
     * @return \EL\ELCoreBundle\Services\SessionService
     */
    public function setPlayer($player)
    {
        $this->session->set('player', $player);
        
        return $this;
    }
    
    /**
     * Save current user in database
     * 
     * @return \EL\ELCoreBundle\Services\SessionService
     */
    public function savePlayer()
    {
        $player = $this->getPlayer();
        
        $newplayer = $this->em->merge($player);
        $this->setPlayer($newplayer);
        $this->em->flush();
        
        return $this;
    }
    
    /**
     * Hash function
     * 
     * @param string $password plain
     * 
     * @return string md5
     */
    public function hashPassword($password)
    {
        $salt = 'Sel de Guérande';
        
        return md5($salt.$password.$salt);
    }
    
    public static function generateGuest($locale = 'en')
    {
        $guest = new Player();
        
        $guest
            ->setPseudo(self::generateGuestName($locale))
            ->setInvited(true)
        ;
        
        return $guest;
    }
    
    public static function generateGuestName($locale = 'en')
    {
        return 'Guest '.rand(10000, 99999);
    }
}
