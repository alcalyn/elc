<?php

namespace EL\CheckersBundle\Checkers;

use EL\CoreBundle\Exception\ELCoreException;
use EL\CoreBundle\Util\Coords;
use EL\CheckersBundle\Entity\CheckersParty;
use EL\CheckersBundle\Checkers\Variant;

class CapturesAnticipator
{
    /**
     * @var CheckersParty
     */
    private $checkersParty;
    
    /**
     * @var Variant
     */
    private $variant;
    
    /**
     * @var integer
     */
    private $boardSize;
    
    /**
     * @var boolean
     */
    private $player;
    
    /**
     * @var array
     */
    private $grid;
    
    /**
     * @var integer
     */
    private $lineForward;
    
    /**
     * Array of coords of each direction
     * 
     * @var array
     */
    private $coordsPatterns;
    
    /**
     * Number of directions piece can move to, 2 or 4
     * 
     * @var integer
     */
    private $sideNumber;
    
    /**
     * @var array
     */
    private $moves;
    
    /**
     * Anticipate all captures move for a player
     * 
     * @param Coords $coords (if set, anticipate moves only for the piece on $coords)
     * @param boolean $player (default is current player turn)
     * 
     * @return array
     */
    public function anticipate(CheckersParty $checkersParty, Coords $coords = null, $player = null)
    {
        $this->checkersParty    = $checkersParty;
        $this->variant          = new Variant($checkersParty->getParameters());
        $this->sideNumber       = $this->variant->getBackwardCapture() ? 4 : 2 ;
        $this->boardSize        = $this->variant->getBoardSize();
        $this->grid             = $checkersParty->getGrid();
        $this->moves            = array();
        $this->player           = (null === $player) ? $this->checkersParty->getCurrentPlayer() : $player ;
        $this->lineForward      = $this->player ? 1 : -1 ;
        $colorMatch             = $this->player ? array(2, 4) : array(1, 3) ;
        
        $this->coordsPatterns = array(
            array(
                new Coords($this->lineForward, 1),
                new Coords($this->lineForward * 2, 2),
            ),
            array(
                new Coords($this->lineForward, - 1),
                new Coords($this->lineForward * 2, - 2),
            ),
            array(
                new Coords(- $this->lineForward, 1),
                new Coords(- $this->lineForward * 2, 2),
            ),
            array(
                new Coords(- $this->lineForward, - 1),
                new Coords(- $this->lineForward * 2, - 2),
            ),
        );
        
        if (null === $coords) {
            for ($line = 0; $line < $this->boardSize; $line++) {
                for ($col = 0; $col < $this->boardSize; $col++) {
                    if (in_array($this->grid[$line][$col], $colorMatch)) {
                        $coords = new Coords($line, $col);

                        $this->anticipateRecursive($this->grid, new Move(0, array($coords)));
                    }
                }
            }
        } else {
            if (in_array($this->grid[$coords->line][$coords->col], $colorMatch)) {
                $this->anticipateRecursive($this->grid, new Move(0, array($coords)));
            } else {
                throw new ELCoreException('piece on $coords is empty or is not owned by player');
            }
        }
        
        return $this->moves;
    }
    
    /**
     * @param array $grid
     * @param \EL\CheckersBundle\Checkers\Move $currentMove
     */
    private function anticipateRecursive(array $grid, Move $currentMove)
    {
        $coordsFrom = end($currentMove->path);
        $pieceFrom = $this->pieceAt($grid, $coordsFrom);
        $jumpAgain = false;
        
        if (!$pieceFrom->isKing()) {
            
            // Simple piece
            for ($i = 0; $i < $this->sideNumber; $i++) {
                
                // Check jump to direction $i
                $coordsJump = $coordsFrom->add($this->coordsPatterns[$i][0]);
                $pieceJump = $this->pieceAt($grid, $coordsJump);
                $coordsTo = $coordsFrom->add($this->coordsPatterns[$i][1]);
                $pieceTo = $this->pieceAt($grid, $coordsTo);
                
                if ((null !== $pieceTo) && !$pieceJump->isFree() && $pieceTo->isFree() && ($pieceJump->getColor() !== $pieceFrom->getColor())) {
                    
                    // Check men jump kings
                    if ($this->variant->getMenJumpKing() || (!$pieceJump->isKing())) {
                        $jumpAgain = true;
                        $newGrid = $grid;
                        $newMove = clone $currentMove;
                        $this->jump($newGrid, $pieceFrom, $coordsFrom, $coordsJump, $coordsTo, $newMove);
                        $this->anticipateRecursive($newGrid, $newMove);
                    }
                }
            }
        } else {
            
            // Kings
            if ($this->variant->getLongRangeKing()) {
                
                // Long range kings (no deep anticipation)
                $jumpAgain = true;
                
                for ($i = 0; $i < 4; $i++) {
                    $coordsJump = null;
                    $pieceJump = null;
                    
                    $generator = $coordsFrom->iterateCoords($this->coordsPatterns[$i][0], $this->boardSize);
                    $continue = true;
                    
                    while ($continue && $generator->valid()) {
                        $coordsJump = $generator->current();
                        $pieceJump = $this->pieceAt($grid, $coordsJump);

                        if (!$pieceJump->isFree()) {
                            if ($pieceJump->getColor() !== $pieceFrom->getColor()) {
                                $generator->next();
                                
                                if ($generator->valid()) {
                                    $coordsTo = $generator->current();

                                    if ($this->pieceAt($grid, $coordsTo)->isFree()) {
                                        $newMove = clone $currentMove;
                                        $newMove->path []= $coordsTo;
                                        $newMove->jumpedPieces []= $pieceJump;
                                        $this->moves []= $newMove;
                                    }
                                }
                            }

                            $continue = false;
                        } else {
                            $generator->next();
                        }
                    }
                }
                
            } else {
                
                // Normal range kings
                for ($i = 0; $i < 4; $i++) {
                    $coordsJump = $coordsFrom->add($this->coordsPatterns[$i][0]);
                    $pieceJump = $this->pieceAt($grid, $coordsJump);

                    if ((null !== $pieceJump) && !$pieceJump->isFree() && ($pieceJump->getColor() !== $pieceFrom->getColor())) {
                        $coordsTo = $coordsFrom->add($this->coordsPatterns[$i][1]);
                        $pieceTo = $this->pieceAt($grid, $coordsTo);

                        if ((null !== $pieceTo) && $pieceTo->isFree()) {
                            $jumpAgain = true;
                            $newGrid = $grid;
                            $newMove = clone $currentMove;
                            $this->jump($newGrid, $pieceFrom, $coordsFrom, $coordsJump, $coordsTo, $newMove);
                            $this->anticipateRecursive($newGrid, $newMove);
                        }
                    }
                }
            }
        }
        
        if (!$jumpAgain && count($currentMove->jumpedPieces)) {
            $this->moves []= $currentMove;
        }
    }
    
    private function jump(array &$grid, Piece $piece, Coords $from, Coords $jump, Coords $to, Move $move)
    {
        $this->pieceAt($grid, $from, Piece::FREE);
        $this->pieceAt($grid, $jump, Piece::FREE);
        $this->pieceAt($grid, $to, $piece);
        $move->path []= $to;
        $move->jumpedPieces []= $jump;
    }
    
    /**
     * Return piece at coords on grid.
     * If $set is set, update grid.
     * 
     * @param array $grid
     * @param \EL\CoreBundle\Util\Coords $coords
     * @param integer|Coords $set
     * 
     * @return \EL\CheckersBundle\Checkers\Piece or null if $coords is outside board
     */
    private function pieceAt(array &$grid, Coords $coords, $set = null)
    {
        if (!$coords->isInsideBoard($this->boardSize)) {
            return null;
        }
        
        if (null === $set) {
            return new Piece($grid[$coords->line][$coords->col]);
        } else {
            $code = ($set instanceof Piece) ? $set->code : $set ;
            return new Piece($grid[$coords->line][$coords->col] = $code);
        }
    }
}
