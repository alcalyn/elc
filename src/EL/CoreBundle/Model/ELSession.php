<?php

namespace EL\CoreBundle\Model;

use Symfony\Component\HttpFoundation\Session\Session;
use EL\CoreBundle\Entity\Player;

class ELSession
{
    /**
     * @var Session
     */
    private $session;
    
    /**
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }
    
    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }
    
    /**
     * @return boolean
     */
    public function hasPlayer()
    {
        return $this->session->has('player');
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
     * @return string
     */
    public function getPlayerPseudo()
    {
        return $this->getPlayer()->getPseudo();
    }
}
