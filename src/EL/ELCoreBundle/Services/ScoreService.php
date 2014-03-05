<?php

namespace EL\ELCoreBundle\Services;

use Doctrine\ORM\EntityManager;
use EL\ELCoreBundle\Model\ELCoreException;
use EL\ELCoreBundle\Entity\Game;
use EL\ELCoreBundle\Entity\GameVariant;
use EL\ELCoreBundle\Entity\Score;
use EL\ELCoreBundle\Entity\Player;

class ScoreService
{
    /**
     * @var EntityManager
     */
    protected $em;
    
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    /**
     * Get game variant from database, create if not exists.
     * if $variantName is not provided, default game variant will be returned
     * 
     * @param Game $game
     * @param string $variantName
     * 
     * @return \EL\ELCoreBundle\Entity\GameVariant
     */
    protected function getGameVariant(Game $game, $variantName = null)
    {
        if (null === $variantName) {
            $variantName = GameVariant::DEFAULT_NAME;
        }
        
        $gameVariant = $this->em
            ->getRepository('ELCoreBundle:GameVariant')
            ->get($game, $variantName)
        ;
        
        if (null === $gameVariant) {
            $gameVariant = new GameVariant();
            
            $gameVariant
                ->setGame($game)
                ->setName($variantName)
            ;
            
            $this->em->persist($gameVariant);
            $this->em->flush();
        }
        
        return $gameVariant;
    }
    
    /**
     * Return default GameVariant if a Game is provided,
     * else return provided GameVariant
     * 
     * @param Game|GameVariant $var
     * 
     * @return \EL\ELCoreBundle\Entity\GameVariant
     */
    protected function asGameVariant($var)
    {
        $this->checkGameOrGameVariant($var);
        
        if ($var instanceof Game) {
            return $this->getGameVariant($var);
        } else {
            return $var;
        }
    }
    
    /**
     * Get Score data for a player and a game variant
     * 
     * @param \EL\ELCoreBundle\Services\Player $player
     * @param Game|GameVariant $game
     * 
     * @return Score
     */
    public function getScoreData(Player $player, $game)
    {
        $gameVariant = $this->asGameVariant($game);
        
        $score = $this->em
                ->getRepository('ELCoreBundle:Score')
                ->get($player, $gameVariant)
        ;
        
        if (null === $score) {
            $score = new Score();
            
            $score
                ->setGameVariant($gameVariant)
                ->setPlayer($player)
            ;
            
            $this->em->persist($score);
            $this->em->flush();
        }
        
        return $score;
    }
    
    /**
     * Check if $var is a Game or GameVariant
     * 
     * @param mixed $var
     * 
     * @throws ELCoreException if $var is not a Game or GameVariant
     */
    protected function checkGameOrGameVariant($var)
    {
        $isGame         = ($var instanceof Game);
        $isGameVariant  = ($var instanceof GameVariant);
        
        if (!($isGame || $isGameVariant)) {
            throw new ELCoreException('Expected instance of Game or GameVariant, got '.get_class($var));
        }
    }
}
