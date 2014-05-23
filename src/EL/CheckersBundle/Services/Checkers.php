<?php

namespace EL\CheckersBundle\Services;

use EL\CoreBundle\Util\Coords;
use EL\CheckersBundle\Checkers\CheckersIllegalMoveException;
use EL\CheckersBundle\Checkers\Variant;
use EL\CheckersBundle\Checkers\Piece;
use EL\CheckersBundle\Entity\CheckersParty;

class Checkers
{
    const WHITE = false;
    const BLACK = true;
    
    /**
     * Array containing variants
     * 
     * @var array
     */
    private $variants = null;
    
    
    /**
     * @return array of Variant
     */
    public function getVariants()
    {
        if (null === $this->variants) {
            $this->variants = array();
            
            // English
            $this->variants[Variant::ENGLISH] = Variant
                    ::createNewVariant()
                    ->setBoardSize(8)
                    ->setFirstPlayer(self::WHITE)
                    ->setMenJumpKing(true)
            ;
            
            // French
            $this->variants[Variant::FRENCH] = Variant
                    ::createNewVariant()
                    ->setBoardSize(10)
                    ->setFirstPlayer(self::WHITE)
                    ->setMenJumpKing(true)
                    ->setLongRangeKing(true)
                    ->setForceCapture(true)
            ;
        }
        
        return $this->variants;
    }
    
    /**
     * Return variant name from $checkersVariant.
     * If not exists, return 'personalized'
     * 
     * @param Variant $checkersVariant
     * 
     * @return string
     */
    public function getVariantName(Variant $checkersVariant)
    {
        foreach ($this->getVariants() as $name => $variant) {
            if ($checkersVariant->equals($variant)) {
                return $name;
            }
        }
        
        return Variant::PERSONALIZED;
    }
    
    /**
     * Create a new grid and fill it following $checkersVariant
     * 
     * @param \EL\CheckersBundle\Checkers\Variant $checkersVariant
     * 
     * @return array
     * 
     * @throws ELCoreException if $boardSize not in 4, 6, 8, 10, 12, 14
     */
    public function initGrid(Variant $checkersVariant)
    {
        $boardSize = $checkersVariant->getBoardSize();
        
        if (!in_array($boardSize, array(4, 6, 8, 10, 12, 14))) {
            throw new ELCoreException('$boardSize must be in 4, 6, 8, 10, 12, 14, got "'.$boardSize.'"');
        }
        
        $squareUsed     = $checkersVariant->getSquareUsed();
        $rightSquare    = $checkersVariant->getRightSquare();
        $grid           = array_fill(0, $boardSize, array_fill(0, $boardSize, Piece::FREE));
        $middle         = floor($boardSize / 2) - 1;
        $shift          = $squareUsed === $rightSquare ? 0 : 1 ;
        
        for ($i = 0; $i < $middle; $i++) {
            for ($j = 0; $j < $boardSize; $j += 2) {
                $grid[$i][$j + (($i + $shift) % 2)] = Piece::BLACK;
                $grid[$boardSize - $i - 1][$j + 1 - (($i + $shift) % 2)] = Piece::WHITE;
            }
        }
        
        return $grid;
    }
    
    /**
     * Perform a piece move on $checkersParty, by $playerPosition,
     * from square $from to $to.
     * 
     * @param \EL\CheckersBundle\Entity\CheckersParty $checkersParty
     * @param integer $playerPosition 0 or 1
     * @param \EL\CoreBundle\Util\Coords $from
     * @param \EL\CoreBundle\Util\Coords $to
     * 
     * @return boolean true
     * 
     * @throws CheckersIllegalMoveException
     */
    public function move(CheckersParty $checkersParty, Coords $from, Coords $to, Player $loggedPlayer)
    {
        $variant        = new Variant($checkersParty->getParameters());
        $boardSize      = $variant->getBoardSize();
        $grid           = $checkersParty->getGrid();
        $pieceFrom      = self::pieceAt($grid, $from);
        $pieceTo        = self::pieceAt($grid, $to);
        
        // Check if there is a piece on from square
        if (Piece::FREE === $pieceFrom) {
            throw new CheckersIllegalMoveException('there is no piece in from: '.$from);
        }
        
        $playerPieces = (($pieceFrom % 2) === 1) ? Piece::WHITE : Piece::BLACK;
        
        // Check if piece moved is not owned by the other player
        if (($playerPieces - 1) === $checkersParty->getCurrentPlayer()) {
            throw new CheckersIllegalMoveException('you cannot move pieces of your opponent');
        }
        
        // Test if from and to are the same
        if ($from->isEqual($to)) {
            throw new CheckersIllegalMoveException('no move detected');
        }
        
        // Check if movement is inside board
        if (!$from->isInsideBoard($boardSize)) {
            throw new CheckersIllegalMoveException('$from must be in board size '.$boardSize.', got '.$from);
        }
        
        // Check if we move piece inside the board
        if (!$to->isInsideBoard($boardSize)) {
            throw new CheckersIllegalMoveException('$to must be in board size '.$boardSize.', got '.$to);
        }
        
        // Check if destination square is an used square
        if ($to->isOdd() xor ($variant->getRightSquare() xor $variant->getSquareUsed())) {
            throw new CheckersIllegalMoveException('you must move diagonnally');
        }
        
        // Check if destination square is not already occupied
        if (Piece::FREE !== $pieceTo) {
            throw new CheckersIllegalMoveException('you cannot move on a not empty square');
        }
        
        if ($pieceFrom < 3) {
            // If piece is not a king
            
            // Check for diagonal move
            if (!$from->isDiagonal($to)) {
                throw new CheckersIllegalMoveException('you must move diagonnaly');
            }
            
            // Check if piece move on neighbor square
            if ($from->distanceToLine($to) > 1) {
                throw new CheckersIllegalMoveException('you cannot move over more than one square');
            }
            
            // Check if piece go forward
            if (($playerPieces === Piece::BLACK) xor (($to->line - $from->line) > 0)) {
                throw new CheckersIllegalMoveException('you cannot move back');
            }
        } else {
            throw new CheckersIllegalMoveException('kings not implemented yet');
        }
        
        $this->pieceAt($grid, $to, $pieceFrom);
        $this->pieceAt($grid, $from, Piece::FREE);
        
        $checkersParty
                ->setGrid($grid)
                ->setCurrentPlayer(!$checkersParty->getCurrentPlayer())
        ;
        
        return true;
    }
    
    public function pieceAt(array &$grid, Coords $square, $set = null)
    {
        if (null === $set) {
            return $grid[$square->line][$square->col];
        } else {
            return $grid[$square->line][$square->col] = $set;
        }
    }
}
