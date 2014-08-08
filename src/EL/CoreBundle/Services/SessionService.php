<?php

namespace EL\CoreBundle\Services;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use EL\CoreBundle\Exception\ELCoreException;
use EL\CoreBundle\Model\ELSession;
use EL\CoreBundle\Exception\LoginException;
use EL\CoreBundle\Entity\Player;

class SessionService
{
    /**
     * @var Session
     */
    private $session;
    
    /**
     * @var ELSession
     */
    private $elSession;
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    
    /**
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(Session $session, EntityManager $em)
    {
        $this->session      = $session;
        $this->elSession    = new ELSession($session);
        $this->em           = $em;
        
        $this->start();
    }
    
    /**
     * Init session, create a guest if first connexion
     */
    public function start()
    {
        $this->session->start();
        
        if ($this->elSession->hasPlayer()) {
            $this->resyncronizePlayer();
        } else {
            $guest = self::generateGuest('en');
            $this->setPlayer($guest);
            $this->savePlayer();
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
        if ($this->isLogged()) {
            $this->session->invalidate();
            $this->session->start();
        } else {
            throw new ELCoreException('Try to logout but not logged');
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
        return $this->elSession->getPlayer();
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
        $this->elSession->setPlayer($player);
        
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
     * recreate it if not exists (when fixtures reloaded)
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
    
    /**
     * @param string $locale
     * 
     * @return \EL\CoreBundle\Entity\Player
     */
    public static function generateGuest($locale = 'en')
    {
        $guest = new Player();
        
        $guest
            ->setPseudo(self::generateGuestName($locale))
            ->setInvited(true)
        ;
        
        return $guest;
    }
    
    /**
     * @param string $locale
     * 
     * @return string
     */
    public static function generateGuestName($locale = 'en')
    {
        return 'Guest '.rand(10000, 99999);
    }
}
