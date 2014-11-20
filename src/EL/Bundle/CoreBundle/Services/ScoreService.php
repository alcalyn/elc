<?php

namespace EL\Bundle\CoreBundle\Services;

use Doctrine\ORM\EntityManager;
use EL\Bundle\CoreBundle\Exception\ELCoreException;
use EL\Core\Entity\Game;
use EL\Core\Entity\GameVariant;
use EL\Core\Entity\Score;
use EL\Core\Entity\Player;

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
     * @return \EL\Core\Entity\GameVariant
     */
    public function getGameVariant(Game $game, $variantName = null)
    {
        if (null === $variantName) {
            $variantName = GameVariant::DEFAULT_NAME;
        }
        
        $gameVariant = $this->em
            ->getRepository('Core:GameVariant')
            ->get($game, $variantName)
        ;
        
        if (null === $gameVariant) {
            $gameVariant = new GameVariant();
            
            $gameVariant
                ->setGame($game)
                ->setName($variantName)
            ;
            
            $this->em->persist($gameVariant);
        }
        
        return $gameVariant;
    }
    
    /**
     * Return default GameVariant if a Game is provided,
     * else return provided GameVariant
     * 
     * @param Game|GameVariant $var
     * 
     * @return \EL\Core\Entity\GameVariant
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
     * @param \EL\Bundle\CoreBundle\Services\Player $player
     * @param Game|GameVariant $game
     * 
     * @return Score
     */
    public function getScoreData(Player $player, $game)
    {
        $gameVariant = $this->asGameVariant($game);
        
        $score = $this->em
                ->getRepository('Core:Score')
                ->get($player, $gameVariant)
        ;
        
        if (null === $score) {
            $score = new Score();
            
            $score
                ->setGameVariant($gameVariant)
                ->setPlayer($player)
            ;
            
            $this->em->persist($score);
        }
        
        return $score;
    }
    
    /**
     * Get Score data for a player and a game variant
     * 
     * @param \EL\Bundle\CoreBundle\Services\Player $players
     * @param Game|GameVariant $game
     * 
     * @return Score[]
     */
    public function getMultipleScoreData(array $players, $game)
    {
        if (0 === count($players)) {
            return array();
        }
        
        $gameVariant = $this->asGameVariant($game);
        
        $scores = $this->em
                ->getRepository('Core:Score')
                ->getMultiple($players, $gameVariant)
        ;
        
        $arrayScores = array();
        
        foreach ($scores as $score) {
            $arrayScores[$score->getPlayer()->getId()] = $score;
        }
        
        return $arrayScores;
    }
    
    /**
     * Add a score badge to a player on a game variant
     * 
     * @param Player $player
     * @param mixed $game
     * @param Score $score
     */
    public function badgePlayer(Player $player, $game, Score $score = null)
    {
        $gameVariant = $this->asGameVariant($game);
        
        if (null === $score) {
            $score = $this->getScoreData($player, $gameVariant);
        }
        
        $player->badge = $score;
    }
    
    /**
     * Add a score badge to multiple players
     * 
     * @param array $players
     * @param mixed $game
     */
    public function badgePlayers(array $players, $game)
    {
        $gameVariant    = $this->asGameVariant($game);
        $scores         = $this->getMultipleScoreData($players, $game);
        
        foreach ($scores as $score) {
            $this->badgePlayer($score->getPlayer(), $gameVariant, $score);
        }
    }
    
    /**
     * Return a rank board of the $game from $offset to $lenth.
     * 
     * Example:
     * getRanking($chessGame, 10);          // return top 10
     * getRanking($chessGame, 100, 300);    // return rank from 300 to 400
     * 
     * @param Game|GameVariant $game
     * @param integer $length number of item from $offset
     * @param integer $offset from which rank to start default 0
     * @return array
     */
    public function getRanking($game, $length = -1, $offset = 0)
    {
        $gameVariant = $this->asGameVariant($game);
        
        $orderRaw   = explode(',', $gameVariant->getGame()->getRankingOrder());
        $order      = array();
        
        foreach ($orderRaw as $data) {
            $tokens = explode(':', $data);
            
            $field      = $tokens[0];
            $direction  = (isset($tokens[1]) && ('d' === $tokens[1])) ? 'desc' : 'asc' ;
            
            $order[$field] = $direction;
        }
        
        return $this->em
                ->getRepository('Core:Score')
                ->getRanking($gameVariant, $order, $length, $offset)
        ;
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
