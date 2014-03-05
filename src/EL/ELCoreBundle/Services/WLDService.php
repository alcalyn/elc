<?php

namespace EL\ELCoreBundle\Services;

use Doctrine\ORM\EntityManager;
use EL\ELCoreBundle\Entity\WLD;
use EL\ELCoreBundle\Entity\Player;
use EL\ELCoreBundle\Entity\Party;
use EL\ELCoreBundle\Entity\Score;
use EL\ELCoreBundle\Entity\Game;
use EL\ELCoreBundle\Entity\GameVariant;

class WLDService extends ScoreService
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }
    
    /**
     * Add a win, loss or draw to $player, and add a time statistic
     * 
     * Examples of uses :
     *      $this->get('el_core.score.wld')->win($player, $game);
     *      $this->get('el_core.score.wld')->draw($player, $game, $party);
     *      $this->get('el_core.score.wld')->update($player, $gameVariant, WLD::WIN, $party);
     * 
     * @param Player            $player
     * @param Game|GameVariant  $game
     * @param integer           $wldValue WLD::WIN, WLD::LOSS or WLD::DRAW
     * @param Party             $party
     * 
     * @return Score score data just updated of $player
     * 
     * @throws ELCoreException if $wldValue not WLD::WIN, WLD::LOSS or WLD::DRAW
     */
    public function update(Player $player, $game, $wldValue, Party $party = null)
    {
        if (!in_array($wldValue, array(WLD::WIN, WLD::LOSS, WLD::DRAW))) {
            throw new ELCoreException('$wldValue must be WLD::WIN, WLD::LOSS or WLD::DRAW, got "'.$wldValue.'"');
        }
        
        /**
         * Initialize variables
         */
        $gameVariant    = $this->asGameVariant($game);
        $scoreData      = $this->getScoreData($player, $gameVariant);
        
        /**
         * Increment score data of player
         */
        switch ($wldValue) {
            case WLD::WIN:
                $scoreData->addWin();
                break;
            
            case WLD::LOSS:
                $scoreData->addLoss();
                break;
            
            case WLD::DRAW:
                $scoreData->addDraw();
                break;
        }
        
        /**
         * Add a statistic point in time
         */
        $wld = new WLD();
        $wld
            ->setPlayer($player)
            ->setGameVariant($gameVariant)
            ->setParty($party)
            ->setValue($wldValue)
            ->setDateCreate(new \DateTime())
        ;
        
        /**
         * Store changes into database
         */
        $this->em->persist($wld);
        
        $this->em->flush();
        
        return $scoreData;
    }
    
    /**
     * Add a win for $player on $game or GameVariant on $party
     * 
     * @param Player            $player
     * @param Game|GameVariant  $game
     * @param Party             $party
     * 
     * @return Score score data just updated of $player
     */
    public function win(Player $player, $game, Party $party = null)
    {
        return $this->update($player, $game, WLD::WIN, $party);
    }
    
    /**
     * Add a loss for $player on $game or GameVariant on $party
     * 
     * @param Player            $player
     * @param Game|GameVariant  $game
     * @param Party             $party
     * 
     * @return type
     */
    public function lose(Player $player, $game, Party $party = null)
    {
        return $this->update($player, $game, WLD::LOSS, $party);
    }
    
    /**
     * Add a draw for $player on $game or GameVariant on $party
     * 
     * @param Player            $player
     * @param Game|GameVariant  $game
     * @param Party             $party
     * 
     * @return Score score data just updated of $player
     */
    public function draw(Player $player, $game, Party $party = null)
    {
        return $this->update($player, $game, WLD::DRAW, $party);
    }
}
