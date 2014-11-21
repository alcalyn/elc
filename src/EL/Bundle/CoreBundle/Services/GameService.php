<?php

namespace EL\Bundle\CoreBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use EL\Core\Entity\Game;
use EL\Bundle\CoreBundle\AbstractGame\Model\ELGameInterface;
use EL\Core\Exception\Exception;

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
     * @var ELGameInterface
     */
    private $gameInterface = null;
    
    
    
    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct($em)
    {
        $this->em = $em;
    }
    
    
    /**
     * @param \EL\Core\Entity\Game $game
     * @return \EL\Bundle\CoreBundle\Services\GameService
     */
    public function setGame(Game $game, Container $container = null)
    {
        $this->game = $game;
        
        if (!is_null($container)) {
            $this->loadGameInterface($container);
        }
        
        return $this;
    }
    
    /**
     * @param string $slug
     * @param string $locale
     * @return \EL\Bundle\CoreBundle\Services\GameService
     */
    public function setGameBySlug($slug, $locale, Container $container = null)
    {
        try {
            $game = $this->em
                    ->getRepository('Core:Game')
                    ->findByLang($locale, $slug)
            ;
        } catch (\Doctrine\ORM\NoResultException $e) {
            throw new Exception('Game "'.$slug.'" unknown');
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
     * Use entity manager from this service to persist an entity
     * 
     * @param \stdClass $entity
     * 
     * @return void
     */
    public function persist($entity)
    {
        return $this->em->persist($entity);
    }
    
    /**
     * Find all parties for this game
     * 
     * @param integer $state of parties, let blank for all parties.
     * 
     * @return array
     */
    public function getParties($state = null)
    {
        $criteria = array(
            'game'  => $this->getGame(),
        );
        
        if (null !== $state) {
            $criteria['state'] = $state;
        }
        
        $parties = $this->em
                ->getRepository('Core:Party')
                ->findBy($criteria)
        ;
        
        return $parties;
    }
    
    /**
     * Return extended game service
     * 
     * @return \EL\Bundle\CoreBundle\AbstractGame\Model\ELGameInterface
     */
    public function getGameInterface()
    {
        $this->needGameInterface();
        return $this->gameInterface;
    }
    
    /**
     * Return extended game service
     * 
     * @return GameService
     */
    public function loadGameInterface(Container $container)
    {
        $this->needGame();
        $gameInterface = $container->get('el_games.'.$this->game->getName());
        
        if ($gameInterface instanceof ELGameInterface) {
            $this->gameInterface = $gameInterface;
            return $this;
        } else {
            throw new Exception('Your game service must implement EL\Bundle\CoreBundle\AbstractGame\Model\ELGameInterface');
        }
    }
    
    
    protected function needGame()
    {
        if (is_null($this->game)) {
            throw new \Exception('GameService : game must be defined by calling setGame');
        }
    }
    
    protected function needGameInterface()
    {
        if (is_null($this->gameInterface)) {
            throw new \Exception('GameService : extended game must be defined by calling setGame');
        }
    }
}
