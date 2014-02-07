<?php

namespace EL\ELCoreBundle\Services;

use EL\ELCoreBundle\Entity\Game;
use Symfony\Component\DependencyInjection\Container;

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
    private $extended_game = null;
    
    
    
    public function __construct($em)
    {
        $this->em = $em;
    }
    
    
    /**
     * @param \EL\ELCoreBundle\Entity\Game $game
     * @return \EL\ELCoreBundle\Services\GameService
     */
    public function setGame(Game $game, Container $container = null)
    {
        $this->game = $game;
        
        if (!is_null($container)) {
            $this->extended_game = $container->get($this->getGameServiceName());
        }
        
        return $this;
    }
    
    /**
     * @param string $slug
     * @param string $locale
     * @return \EL\ELCoreBundle\Services\GameService
     */
    public function setGameBySlug($slug, $locale, Container $container = null)
    {
        $game = $this->em
                ->getRepository('ELCoreBundle:Game')
                ->findByLang($locale, $slug)
        ;
        
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
     * @return \EL\ELAbstractGameBundle\Model\ELGameInterface
     */
    public function getExtendedGame()
    {
        $this->needExtendedGame();
        return $this->extended_game;
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
        if (is_null($this->extended_game)) {
            throw new \Exception('GameService : extended game must be defined by calling setGame');
        }
    }
}
