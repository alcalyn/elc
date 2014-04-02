<?php

namespace EL\CoreBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use EL\CoreBundle\Entity\Game;
use EL\CoreBundle\Exception\ELCoreException;

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
    
    /**
     * Extended Game service
     * 
     * @var ELAbstractGame\Model\ELGameInterface
     */
    private $extendedGame = null;
    
    
    
    public function __construct($em)
    {
        $this->em = $em;
    }
    
    
    /**
     * @param \EL\CoreBundle\Entity\Game $game
     * @return \EL\CoreBundle\Services\GameService
     */
    public function setGame(Game $game, Container $container = null)
    {
        $this->game = $game;
        
        if (!is_null($container)) {
            $this->loadExtendedGame($container);
        }
        
        return $this;
    }
    
    /**
     * @param string $slug
     * @param string $locale
     * @return \EL\CoreBundle\Services\GameService
     */
    public function setGameBySlug($slug, $locale, Container $container = null)
    {
        try {
            $game = $this->em
                    ->getRepository('CoreBundle:Game')
                    ->findByLang($locale, $slug)
            ;
        } catch (\Doctrine\ORM\NoResultException $e) {
            throw new ELCoreException('Game "'.$slug.'" unknown');
        }
        
        $this->setGame($game, $container);
        
        return $this;
    }
    
    /**
     * @return Game
     */
    public function getGame()
    {
        $this->needGame();
        return $this->game;
    }
    
    /**
     * Return extended game service
     * 
     * @return \EL\AbstractGameBundle\Model\ELGameInterface
     */
    public function getExtendedGame()
    {
        $this->needExtendedGame();
        return $this->extendedGame;
    }
    
    /**
     * Return extended game service
     * 
     * @return \EL\AbstractGameBundle\Model\ELGameInterface
     */
    public function loadExtendedGame(Container $container)
    {
        $this->needGame();
        $this->extendedGame = $container->get($this->getGameServiceName());
        return $this;
    }
    
    /**
     * @return string
     */
    public function getGameServiceName(Game $game = null)
    {
        if (is_null($game)) {
            $this->needGame();
            return 'el_games.'.$this->game->getName();
        } else {
            return 'el_games.'.$game->getName();
        }
    }
    
    
    protected function needGame()
    {
        if (is_null($this->game)) {
            throw new \Exception('GameService : game must be defined by calling setGame');
        }
    }
    
    protected function needExtendedGame()
    {
        if (is_null($this->extendedGame)) {
            throw new \Exception('GameService : extended game must be defined by calling setGame');
        }
    }
}
