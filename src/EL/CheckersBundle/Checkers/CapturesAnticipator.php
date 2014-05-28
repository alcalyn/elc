<?php

namespace EL\CheckersBundle\Checkers;

use EL\CheckersBundle\Entity\CheckersParty;
use EL\CheckersBundle\Checkers\Variant;
use EL\CoreBundle\Util\Coords;

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
     * @var array
     */
    private $coordsPatterns;
    
    /**
     * @var integer
     */
    private $sideNumber;
    
    /**
     * @var array
     */
    private $moves;
    
    /**
     * Constructor.
     * 
     * @param \EL\CheckersBundle\Entity\CheckersParty $checkersParty
     */
    public function __construct(CheckersParty $checkersParty)
    {
        $this->checkersParty = $checkersParty;
        $this->variant = new Variant($checkersParty->getParameters());
        $this->sideNumber = $this->variant->getBackwardCapture() ? 4 : 2 ;
        $this->boardSize = $this->variant->getBoardSize();
        $this->grid = $checkersParty->getGrid();
    }
    
    /**
     * Anticipate all captures move for a player
     * 
     * @param boolean $player (default is current player turn)
     * 
     * @return array
     */
    public function anticipate($player = null)
    {
        $this->moves        = array();
        $this->player       = (null === $player) ? $this->checkersParty->getCurrentPlayer() : $player ;
        $this->lineForward  = $this->player ? -1 : 1 ;
        $colorMatch         = $this->player ? array(2, 4) : array(1, 3) ;
        
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
        
        for ($line = 0; $line < $this->boardSize; $line++) {
            for ($col = 0; $col < $this->boardSize; $col++) {
                if (in_array($this->grid[$line][$col], $colorMatch)) {
                    $coords = new Coords($line, $col);
                    $this->anticipateRecursive($this->grid, new Move(0, array($coords)));
                }
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
        
        // Simple piece
        if (!$pieceFrom->isKing()) {
            
            for ($i = 0; $i < $this->sideNumber; $i++) {
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
