<?php

namespace EL\ELTicTacToeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EL\PhaxBundle\Model\PhaxAction;
use EL\ELCoreBundle\Entity\WLD;
use EL\ELCoreBundle\Entity\Party as CoreParty;

class TicTacToeAjaxController extends Controller
{
    
    public function refreshAction(PhaxAction $phax_action)
    {
        $em = $this->getDoctrine()->getManager();
        
        /**
         * Load Tic Tac Toe party
         */
        $extendedParty = $em
                ->getRepository('ELTicTacToeBundle:Party')
                ->findOneByExtendedPartyId($phax_action->extended_party_id)
        ;
        
        $last_party_end = $extendedParty->getLastPartyEnd();
        
        /**
         * If we are waiting for new grid
         */
        if (null !== $last_party_end) {
            /**
             * Check if we have wait more than 3 seconds
             */
            $now        = new \DateTime();
            $new_time   = clone $last_party_end;
            
            $new_time->add(new \DateInterval('PT3S'));
            
            /**
             * If we have wait enough time, check for party end, or clean the grid and restart a new
             */
            if ($now > $new_time) {
                $party_service = $this
                    ->get('el_core.party')
                    ->setParty($extendedParty->getParty())
                ;
                
                $core_party = $party_service->getParty();

                foreach ($core_party->getSlots() as $slot) {
                    if ($slot->getScore() >= 2) {
                        $party_service->end();
                        break;
                    }
                }
                
                if (CoreParty::ENDED !== $core_party->getState()) {
                    $extendedParty->setGrid('---------');
                }
                
                $extendedParty->setLastPartyEnd(null);
                $em->flush();
            }
        }
        
        
        $winner = self::winner($extendedParty->getGrid());
        
        return $this->get('phax')->reaction(array(
            'party'     => $extendedParty->jsonSerialize(),
            'winner'    => $winner,
        ));
    }
    
    
    public function tickAction(PhaxAction $phax_action)
    {
        $em = $this->getDoctrine()->getManager();
        
        $extendedParty = $em
                ->getRepository('ELTicTacToeBundle:Party')
                ->findOneByExtendedPartyId($phax_action->extended_party_id)
        ;
        
        $phax       = $this->get('phax');
        $grid       = $extendedParty->getGrid();
        $coords     = $phax_action->get('coords');
        $player     = $this->get('el_core.session')->getPlayer();
        $baseParty  = $extendedParty->getParty();
        $slot       = $baseParty->getSlot($extendedParty->getCurrentPlayer() - 1);
        
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
         * Check if we are waiting for next grid
         */
        if (null !== $extendedParty->getLastPartyEnd()) {
            return $phax->error('grid finished, waiting for next grid');
        }
        
        /**
         * Check for player turn
         */
        if ($slot->getPlayer()->getId() !== $player->getId()) {
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
        $grid[$index] = $extendedParty->getCurrentPlayer() == 1 ? 'X' : 'O' ;
        
        $extendedParty
            ->setGrid($grid)
            ->setCurrentPlayer(3 - $extendedParty->getCurrentPlayer())
        ;
        
        /**
         * Check for win, lose or draw
         */
        $winner = self::winner($grid);
        
        if (null !== $winner) {
            $wld_service = $this->get('el_core.score.wld');
            $elo_service = $this->get('el_core.score.elo');
            
            if ($winner !== '-') {
                /**
                 * Party with a winner
                 */
                $baseParty
                    ->getSlot($winner === 'X' ? 0 : 1)
                    ->addScore()
                ;
                
                $winnerPlayer = $baseParty->getSlot($winner === 'X' ? 0 : 1)->getPlayer();
                $looserPlayer = $baseParty->getSlot($winner === 'O' ? 0 : 1)->getPlayer();
                
                $wld_service->win($winnerPlayer, $baseParty->getGame(), $baseParty);
                $wld_service->lose($looserPlayer, $baseParty->getGame(), $baseParty);
                
                $elo_service->win($winnerPlayer, $looserPlayer, $baseParty->getGame(), $baseParty);
            } else {
                /**
                 * Draw party
                 */
                foreach ($baseParty->getSlots() as $slot) {
                    $wld_service->draw($slot->getPlayer(), $baseParty->getGame(), $baseParty);
                }
                
                $elo_service->draw(
                    $baseParty->getSlot(0)->getPlayer(),
                    $baseParty->getSlot(1)->getPlayer(),
                    $baseParty->getGame(),
                    $baseParty
                );
            }
            
            $extendedParty->setLastPartyEnd(new \DateTime());
        }
        
        /**
         * Save the grid
         */
        $this->get('el_core.illflushitlater')
            ->persist($extendedParty)
            ->flush()
        ;
        
        return $phax->reaction(array(
            'party'     => $extendedParty->jsonSerialize(),
            'winner'    => $winner,
        ));
    }
    
    
    /**
     * Check if we have winner or draw party
     * 
     * @param string $grid
     * @return mixed
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
    
    private static function brochette($grid, $a, $b, $c)
    {
        return
            $grid[$a] !== '-' &&
            $grid[$a] === $grid[$b] &&
            $grid[$a] === $grid[$c] ;
    }
}
