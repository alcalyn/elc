<?php

namespace EL\CoreBundle\Services;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use EL\CoreBundle\Exception\LoginException;
use EL\CoreBundle\Entity\Player;

class SessionService
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;
    
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    
    
    
    public function __construct(Session $session, EntityManager $em)
    {
        $this->session          = $session;
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
            $this->resyncronizePlayer();
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
     * 
     * @return boolean
     * 
     * @throws LoginException
     */
    public function login($pseudo, $password)
    {
        $player = $this->em
                ->getRepository('CoreBundle:Player')
                ->findOneBy(array(
                    'pseudo'    => $pseudo,
                    'invited'   => false,
                    'bot'       => false,
                ))
        ;
        
        if (null === $player) {
            throw new LoginException('loginerror.pseudo.not.found', LoginException::LOGIN_PSEUDO_NOT_FOUND);
        }
        
        if ($player->getPasswordHash() !== $this->hashPassword($password)) {
            throw new LoginException('loginerror.password.invalid', LoginException::LOGIN_PASSWORD_INVALID);
        }
        
        $this->setPlayer($player);

        return true;
    }
    
    /**
     * Logout
     * 
     * @return \EL\CoreBundle\Services\SessionService
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
            throw new LoginException('signuperror.already.logged', LoginException::LOGIN_ALREADY_LOGGED);
        }
        
        if ($this->pseudoExists($pseudo)) {
            throw new LoginException('signuperror.pseudo.unavailable', LoginException::LOGIN_PSEUDO_UNAVAILABLE);
        }
        
        $player = $this->getPlayer();
        $player
                ->setPseudo($pseudo)
                ->setPasswordHash($this->hashPassword($password))
                ->setInvited(false)
        ;
        
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
                ->getRepository('CoreBundle:Player')
                ->pseudoCount($pseudo)
        ;
        
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
     * @return \EL\CoreBundle\Services\SessionService
     */
    public function setPlayer($player)
    {
        $this->session->set('player', $player);
        
        return $this;
    }
    
    /**
     * Save current user in database
     * 
     * @return \EL\CoreBundle\Services\SessionService
     */
    public function savePlayer()
    {
        $this->resyncronizePlayer();
        
        return $this;
    }
    
    /**
     * Syncronize player instance in session with entitymanager,
     * recreate it if not exists
     */
    private function resyncronizePlayer()
    {
        $oldPlayer = $this->getPlayer();
        
        try {
            $newplayer = $this->em->merge($oldPlayer);
            $this->setPlayer($newplayer);
        } catch (\Doctrine\ORM\EntityNotFoundException $ex) {
            $this->em->persist($oldPlayer);
            $this->em->flush($oldPlayer);
        }
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
