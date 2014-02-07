<?php

namespace EL\ELTicTacToeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EL\PhaxBundle\Model\PhaxAction;

class TicTacToeAjaxController extends Controller
{
    
    public function refreshAction(PhaxAction $phax_action)
    {
        $em = $this->getDoctrine()->getManager();
        
        $party = $em
                ->getRepository('ELTicTacToeBundle:Party')
                ->findOneByExtendedPartyId($phax_action->extended_party_id)
        ;
        
        $winner = self::winner($party->getGrid());
        
        return $this->get('phax')->reaction(array(
            'party'     => $party->jsonSerialize(),
            'winner'    => $winner,
        ));
    }
    
    
    public function tickAction(PhaxAction $phax_action)
    {
        $em = $this->getDoctrine()->getManager();
        
        $party = $em
                ->getRepository('ELTicTacToeBundle:Party')
                ->findOneByExtendedPartyId($phax_action->extended_party_id)
        ;
        
        $phax   = $this->get('phax');
        $grid   = $party->getGrid();
        $coords = $phax_action->get('coords');
        $player = $this->getUser();
        $slot   = $party->getParty()->getSlot($party->getCurrentPlayer() - 1);
        
        if (is_null($party->getCurrentPlayer())) {
            return $phax->error('error, current player is null');
        }
        
        if (is_null($slot)) {
            return $phax->error('no slot at position '.$party->getCurrentPlayer());
        }
        
        if (is_null($slot->getPlayer())) {
            return $phax->error('no player at slot '.$party->getCurrentPlayer());
        }
        
        if ($slot->getPlayer()->getId() !== $player->getId()) {
            return $phax->error('not your turn');
        }
        
        if (is_null($coords)) {
            return $phax->error('coords undefined');
        }
        
        $line   = intval($coords['line']);
        $col    = intval($coords['col']);
        
        if ($line < 0 || $line > 2 || $col < 0 || $col > 2) {
            return $phax->error('coords out of range : '.$line.' ; '.$col);
        }
        
        $index = ($line * 3) + ($col % 3);
        
        if ($grid[$index] !== '-') {
            return $phax->error(
                'case already checked by '.$grid[$index].' in grid '.$grid.' at position '.$line.' ; '.$col
            );
        }
        
        $grid[$index] = $party->getCurrentPlayer() == 1 ? 'X' : 'O' ;
        
        $winner = self::winner($grid);
        
        if (is_null($winner)) {
            $party
                ->setCurrentPlayer(3 - $party->getCurrentPlayer())
                ->setGrid($grid)
            ;
        } else {
            if ($winner !== '-') {
                $score = $party
                        ->getParty()
                        ->getSlot($winner === 'X' ? 0 : 1)
                        ->addScore()
                        ->getScore()
                ;
                
                if ($score >= 2) {
                    $this
                        ->get('el_core.party')
                        ->setParty($party->getParty())
                        ->end()
                    ;
                }
            }
            
            $party->setGrid('---------');
        }
        
        $this->get('el_core.illflushitlater')
            ->persist($party)
            ->flush()
        ;
        
        return $phax->reaction(array(
            'party'        => $party->jsonSerialize(),
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
        if (self::brochette($grid, 0, 1, 2)) return $grid[0];
        if (self::brochette($grid, 3, 4, 5)) return $grid[3];
        if (self::brochette($grid, 6, 7, 8)) return $grid[6];
        
        if (self::brochette($grid, 0, 3, 6)) return $grid[0];
        if (self::brochette($grid, 1, 4, 7)) return $grid[1];
        if (self::brochette($grid, 2, 5, 8)) return $grid[2];
        
        if (self::brochette($grid, 0, 4, 8)) return $grid[0];
        if (self::brochette($grid, 2, 4, 6)) return $grid[2];
        
        if (strpos($grid, '-') === false) {
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
