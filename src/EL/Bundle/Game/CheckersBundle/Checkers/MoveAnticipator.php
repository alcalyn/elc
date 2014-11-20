<?php

namespace EL\Bundle\Game\CheckersBundle\Checkers;

use EL\Bundle\CoreBundle\Exception\ELCoreException;
use EL\Bundle\CoreBundle\Util\Coords;
use EL\Game\Checkers\Entity\CheckersParty;
use EL\Bundle\Game\CheckersBundle\Checkers\Variant;

class MoveAnticipator
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
     * Return true if $player (default is current) can move
     * 
     * @param \EL\Game\Checkers\Entity\CheckersParty $checkersParty
     * 
     * @return boolean
     */
    public function canMove(CheckersParty $checkersParty)
    {
        $this->checkersParty    = $checkersParty;
        $this->variant          = new Variant($checkersParty->getParameters());
        $this->boardSize        = $this->variant->getBoardSize();
        $this->grid             = $checkersParty->getGrid();
        $this->player           = $this->checkersParty->getCurrentPlayer();
        $this->lineForward      = $this->player ? 1 : -1 ;
        $colorMatch             = $this->player ? array(2, 4) : array(1, 3) ;
        $this->coordsPatterns   = $this->initCoordsPattern();
        
        for ($line = 0; $line < $this->boardSize; $line++) {
            for ($col = 0; $col < $this->boardSize; $col++) {
                if (in_array($this->grid[$line][$col], $colorMatch)) {
                    $coords = new Coords($line, $col);
                    $piece = $this->pieceAt($this->grid, $coords);
                    $sides = $piece->isKing() ? 4 : 2 ;
                    
                    for ($i = 0; $i < $sides; $i++) {
                        $side = $this->pieceAt($this->grid, $coords->add($this->coordsPatterns[$i][0]));

                        if ((null !== $side) && $side->isFree()) {
                            return true;
                        }
                    }
                }
            }
        }
        
        // If no simple move can be done, check if at least one capture can be
        return count($this->anticipateCaptures($checkersParty)) > 0;
    }
    
    /**
     * Anticipate all captures move for a player
     * 
     * @param CheckersParty $checkersParty
     * @param Coords $coords (if set, anticipate moves only for the piece on $coords)
     * 
     * @return array of Move
     */
    public function anticipateCaptures(CheckersParty $checkersParty, Coords $coords = null)
    {
        $this->checkersParty    = $checkersParty;
        $this->variant          = new Variant($checkersParty->getParameters());
        $this->sideNumber       = $this->variant->getBackwardCapture() ? 4 : 2 ;
        $this->boardSize        = $this->variant->getBoardSize();
        $this->grid             = $checkersParty->getGrid();
        $this->moves            = array();
        $this->player           = $this->checkersParty->getCurrentPlayer();
        $this->lineForward      = $this->player ? 1 : -1 ;
        $colorMatch             = $this->player ? array(2, 4) : array(1, 3) ;
        $this->coordsPatterns   = $this->initCoordsPattern();
        
        if (null === $coords) {
            // anticipate for every pieces
            for ($line = 0; $line < $this->boardSize; $line++) {
                for ($col = 0; $col < $this->boardSize; $col++) {
                    if (in_array($this->grid[$line][$col], $colorMatch)) {
                        $coords = new Coords($line, $col);

                        $this->anticipateCapturesRecursive($this->grid, new Move(0, array($coords)));
                    }
                }
            }
        } else {
            // Anticipate only for piece at $coords
            if (in_array($this->grid[$coords->line][$coords->col], $colorMatch)) {
                $this->anticipateCapturesRecursive($this->grid, new Move(0, array($coords)));
            } else {
                throw new ELCoreException('piece on $coords is empty or is not owned by player');
            }
        }
        
        return $this->moves;
    }
    
    /**
     * @param array $grid
     * @param \EL\Bundle\Game\CheckersBundle\Checkers\Move $currentMove
     */
    private function anticipateCapturesRecursive(array $grid, Move $currentMove)
    {
        $coordsFrom = end($currentMove->path);
        $pieceFrom = $this->pieceAt($grid, $coordsFrom);
        $jumpAgain = false;
        
        if (null === $pieceFrom) {
            throw new ELCoreException('No pieces at '.$coordsFrom);
        }
        
        if (!$pieceFrom->isKing()) {
            
            // Simple piece
            for ($i = 0; $i < $this->sideNumber; $i++) {
                
                // Check jump to direction $i
                $coordsJump = $coordsFrom->add($this->coordsPatterns[$i][0]);
                $pieceJump = $this->pieceAt($grid, $coordsJump);
                $coordsTo = $coordsFrom->add($this->coordsPatterns[$i][1]);
                $pieceTo = $this->pieceAt($grid, $coordsTo);
                
                if (
                        (null !== $pieceTo) &&
                        !$pieceJump->isFree() &&
                        $pieceTo->isFree() &&
                        ($pieceJump->getColor() !== $pieceFrom->getColor())
                ) {
                    // Check men jump kings
                    if ($this->variant->getMenJumpKing() || (!$pieceJump->isKing())) {
                        $jumpAgain = true;
                        $newGrid = $grid;
                        $newMove = clone $currentMove;
                        $this->jump($newGrid, $pieceFrom, $coordsFrom, $coordsJump, $coordsTo, $newMove);
                        $this->anticipateCapturesRecursive($newGrid, $newMove);
                    }
                }
            }
        } else {
            
            // Kings
            if ($this->variant->getLongRangeKing()) {
                
                // Long range kings (no deep anticipation)
                $jumpAgain = true;
                
                for ($i = 0; $i < 4; $i++) {
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
                                        $newMove->jumpedCoords []= $coordsJump;
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

                    if (
                            (null !== $pieceJump) &&
                            !$pieceJump->isFree() &&
                            ($pieceJump->getColor() !== $pieceFrom->getColor())
                    ) {
                        $coordsTo = $coordsFrom->add($this->coordsPatterns[$i][1]);
                        $pieceTo = $this->pieceAt($grid, $coordsTo);

                        if ((null !== $pieceTo) && $pieceTo->isFree()) {
                            $jumpAgain = true;
                            $newGrid = $grid;
                            $newMove = clone $currentMove;
                            $this->jump($newGrid, $pieceFrom, $coordsFrom, $coordsJump, $coordsTo, $newMove);
                            $this->anticipateCapturesRecursive($newGrid, $newMove);
                        }
                    }
                }
            }
        }
        
        if (!$jumpAgain && count($currentMove->jumpedCoords)) {
            $this->moves []= $currentMove;
        }
    }
    
    private function jump(array &$grid, Piece $piece, Coords $from, Coords $jump, Coords $to, Move $move)
    {
        $this->pieceAt($grid, $from, Piece::FREE);
        $this->pieceAt($grid, $jump, Piece::FREE);
        $this->pieceAt($grid, $to, $piece);
        $move->path []= $to;
        $move->jumpedCoords []= $jump;
    }
    
    /**
     * Return piece at coords on grid.
     * If $set is set, update grid.
     * 
     * @param array $grid
     * @param \EL\Bundle\CoreBundle\Util\Coords $coords
     * @param integer|Piece $set
     * 
     * @return \EL\Bundle\Game\CheckersBundle\Checkers\Piece or null if $coords is outside board
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
    
    /**
     * Init an array of precalculated move
     * 
     * @return array
     */
    private function initCoordsPattern()
    {
        return array(
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
    }
}
