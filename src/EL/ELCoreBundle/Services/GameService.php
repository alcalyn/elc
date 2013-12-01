<?php

namespace EL\ELCoreBundle\Services;

use EL\ELCoreBundle\Entity\Game;


class GameService
{
    
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;
    
    /**
     * @var Game 
     */
    private $game = null;
    
    
    
    public function __construct($em)
    {
        $this->em = $em;
    }
    
    
    /**
     * @param \EL\ELCoreBundle\Entity\Game $game
     * @return \EL\ELCoreBundle\Services\GameService
     */
    public function setGame(Game $game)
    {
        $this->game = $game;
        return $this;
    }
    
    /**
     * @param string $slug
     * @param string $locale
     * @return \EL\ELCoreBundle\Services\GameService
     */
    public function setGameBySlug($slug, $locale)
    {
        $game = $this->em
                ->getRepository('ELCoreBundle:Game')
                ->findByLang($locale, $slug);
        
        $this->setGame($game);
        return $this;
    }
    
    /**
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }
    
    
    /**
     * @return string
     */
    public function getGameServiceName()
    {
        return 'el_games.'.$this->game->getName();
    }
    
    
    protected function needGame()
    {
        if (is_null($this->getGame())) {
            throw new \Exception('PartyService : game must be defined');
        }
    }
}