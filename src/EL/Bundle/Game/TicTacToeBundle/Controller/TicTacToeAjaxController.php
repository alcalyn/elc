<?php

namespace EL\Bundle\Game\TicTacToeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phax\CoreBundle\Model\PhaxAction;
use EL\Core\Entity\Party as CoreParty;
use EL\Game\TicTacToe\Entity\TicTacToeParty;

class TicTacToeAjaxController extends Controller
{
    
    public function refreshAction(PhaxAction $phaxAction)
    {
        $em = $this->getDoctrine()->getManager();
        
        /**
         * Load Tic Tac Toe party
         */
        $extendedParty = $em
                ->getRepository('TicTacToe:TicTacToeParty')
                ->findOneByExtendedPartyId($phaxAction->extendedPartyId)
        ;
        
        $lastPartyEnd = $extendedParty->getLastPartyEnd();
        
        /**
         * If we are waiting for new grid
         */
        if (null !== $lastPartyEnd) {
            /**
             * Check if we have wait more than 3 seconds
             */
            $now        = new \DateTime();
            $newTime    = clone $lastPartyEnd;
            
            $newTime->add(new \DateInterval('PT3S'));
            
            /**
             * If we have wait enough time, check for party end, or clean the grid and restart a new
             */
            if ($now > $newTime) {
                $partyService = $this
                    ->get('el_core.party')
                    ->setParty($extendedParty->getParty())
                ;
                
                $coreParty = $partyService->getParty();
                
                if (TicTacToeParty::END_ON_PARTIES_NUMBER === $extendedParty->getVictoryCondition()) {
                    if ($extendedParty->getPartyNumber() >= $extendedParty->getNumberOfParties()) {
                        $partyService->end();
                    }
                } else {
                    $victoriesCount = 0;
                    
                    foreach ($coreParty->getSlots() as $slot) {
                        $victoriesCount += $slot->getScore();
                    }
                    
                    if (TicTacToeParty::END_ON_WINS_NUMBER === $extendedParty->getVictoryCondition()) {
                        if ($victoriesCount >= $extendedParty->getNumberOfParties()) {
                            $partyService->end();
                        }
                    } elseif (TicTacToeParty::END_ON_DRAWS_NUMBER === $extendedParty->getVictoryCondition()) {
                        $drawsCount = $extendedParty->getPartyNumber() - $victoriesCount;
                        
                        if ($drawsCount >= $extendedParty->getNumberOfParties()) {
                            $partyService->end();
                        }
                    }
                }
                
                if (CoreParty::ENDED !== $coreParty->getState()) {
                    $extendedParty->setGrid('---------');
                    $extendedParty->setPartyNumber($extendedParty->getPartyNumber() + 1);
                }
                
                $extendedParty->setLastPartyEnd(null);
            }
        }
        
        
        $winner = self::winner($extendedParty->getGrid());
        
        return $this->get('phax')->reaction(array(
            'party'     => $extendedParty->jsonSerialize(),
            'winner'    => $winner,
        ));
    }
    
    
    public function tickAction(PhaxAction $phaxAction)
    {
        $em = $this->getDoctrine()->getManager();
        
        $extendedParty = $em
                ->getRepository('TicTacToe:TicTacToeParty')
                ->findOneByExtendedPartyId($phaxAction->extendedPartyId)
        ;
        
        $phax       = $this->get('phax');
        $grid       = $extendedParty->getGrid();
        $coords     = $phaxAction->get('coords');
        $player     = $this->get('el_core.session')->getPlayer();
        $baseParty  = $extendedParty->getParty();
        $slot       = $baseParty->getSlots()->get($extendedParty->getCurrentPlayer());
        
        /**
         * Check inputs
         */
        if (null === $extendedParty->getCurrentPlayer()) {
            return $phax->error('error, current player is null');
        }
        
        if (null === $slot) {
            return $phax->error('no slot at position '.$extendedParty->getCurrentPlayer());
        }
        
        if (null === $slot->getPlayer()) {
            return $phax->error('no player at slot '.$extendedParty->getCurrentPlayer());
        }
        
        if (null === $coords) {
            return $phax->error('coords undefined');
        }
        
        /**
         * Check if party is still active
         */
        if ($baseParty->getState() !== CoreParty::ACTIVE) {
            return $phax->error('party.has.ended');
        }
        
        /**
         * Check if we are waiting for next grid
         */
        if (null !== $extendedParty->getLastPartyEnd()) {
            return $phax->error('grid finished, waiting for next grid');
        }
        
        /**
         * Check for player turn
         */
        if ($slot->getPlayer() !== $player) {
            return $phax->error('not your turn');
        }
        
        /**
         * Check if case exists
         */
        $line   = intval($coords['line']);
        $col    = intval($coords['col']);
        
        if ($line < 0 || $line > 2 || $col < 0 || $col > 2) {
            return $phax->error('coords out of range : '.$line.' ; '.$col);
        }
        
        /**
         * Check if case empty
         */
        $index = ($line * 3) + ($col % 3);
        
        if ($grid[$index] !== '-') {
            return $phax->error(
                'case already checked by '.$grid[$index].' in grid '.$grid.' at position '.$line.' ; '.$col
            );
        }
        
        /**
         * Tick the case
         */
        $grid[$index] = $extendedParty->getCurrentPlayer() == TicTacToeParty::PLAYER_X ? 'X' : 'O' ;
        
        $extendedParty
            ->setGrid($grid)
            ->setCurrentPlayer(1 - $extendedParty->getCurrentPlayer())
        ;
        
        /**
         * Check for win, lose or draw
         */
        $winner = self::winner($grid);
        
        if (null !== $winner) {
            $extendedParty->setLastPartyEnd(new \DateTime());
            
            $wldService = $this->get('el_core.score.wld');
            $eloService = $this->get('el_core.score.elo');
            
            if ($winner !== '-') {
                /**
                 * Party with a winner
                 */
                $baseParty
                    ->getSlots()->get($winner === 'X' ? 0 : 1)
                    ->addScore()
                ;
                
                $winnerPlayer = $baseParty->getSlots()->get($winner === 'X' ? 0 : 1)->getPlayer();
                $looserPlayer = $baseParty->getSlots()->get($winner === 'O' ? 0 : 1)->getPlayer();
                
                $wldService->win($winnerPlayer, $baseParty->getGame(), $baseParty);
                $wldService->lose($looserPlayer, $baseParty->getGame(), $baseParty);
                
                $eloService->win($winnerPlayer, $looserPlayer, $baseParty->getGame(), $baseParty);
            } else {
                /**
                 * Draw party
                 */
                foreach ($baseParty->getSlots() as $slot) {
                    $wldService->draw($slot->getPlayer(), $baseParty->getGame(), $baseParty);
                }
                
                $eloService->draw(
                    $baseParty->getSlots()->get(0)->getPlayer(),
                    $baseParty->getSlots()->get(1)->getPlayer(),
                    $baseParty->getGame(),
                    $baseParty
                );
            }
        }
        
        /**
         * Save the grid
         */
        $em->persist($extendedParty);
        
        return $phax->reaction(array(
            'party'     => $extendedParty->jsonSerialize(),
            'winner'    => $winner,
        ));
    }
    
    
    /**
     * Check if we have winner or draw party
     * 
     * @param string $grid
     * @return string|null
     *             'X'        => X won
     *             'O'        => O won
     *             '-'        => draw
     *             null    => party not finished
     */
    private static function winner($grid)
    {
        /**
         * Check for winner
         */
        if (self::brochette($grid, 0, 1, 2)) return $grid[0];
        if (self::brochette($grid, 3, 4, 5)) return $grid[3];
        if (self::brochette($grid, 6, 7, 8)) return $grid[6];
        
        if (self::brochette($grid, 0, 3, 6)) return $grid[0];
        if (self::brochette($grid, 1, 4, 7)) return $grid[1];
        if (self::brochette($grid, 2, 5, 8)) return $grid[2];
        
        if (self::brochette($grid, 0, 4, 8)) return $grid[0];
        if (self::brochette($grid, 2, 4, 6)) return $grid[2];
        
        /**
         * Check for draw
         */
        if (false === strpos($grid, '-')) {
            return '-';
        } else {
            return null;
        }
    }
    
    /**
     * @param string $grid
     * @param integer $a
     * @param integer $b
     * @param integer $c
     */
    private static function brochette($grid, $a, $b, $c)
    {
        return
            $grid[$a] !== '-' &&
            $grid[$a] === $grid[$b] &&
            $grid[$a] === $grid[$c] ;
    }
}
