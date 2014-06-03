<?php

namespace EL\CheckersBundle\Services;

use EL\CoreBundle\Util\Coords;
use EL\CheckersBundle\Checkers\CheckersIllegalMoveException;
use EL\CheckersBundle\Checkers\Variant;
use EL\CheckersBundle\Checkers\Piece;
use EL\CheckersBundle\Checkers\Move;
use EL\CheckersBundle\Entity\CheckersParty;
use EL\CheckersBundle\Checkers\CapturesAnticipator;

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
                    ->setForceCapture(true)
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
     * @return Move performed
     * 
     * @throws CheckersIllegalMoveException
     */
    public function move(CheckersParty $checkersParty, Coords $from, Coords $to)
    {
        $variant        = new Variant($checkersParty->getParameters());
        $boardSize      = $variant->getBoardSize();
        $grid           = $checkersParty->getGrid();
        $pieceFrom      = $this->pieceAt($grid, $from);
        $pieceTo        = $this->pieceAt($grid, $to);
        $playerPieces   = $pieceFrom->getColor();
        $lastMove       = Move::jsonDeserialize(json_decode($checkersParty->getLastMove()));
        
        // Create new current move, or keep the last if we are in a multiple capture phase
        if ($lastMove->multipleCapture) {
            $move = $lastMove;
            if (end($move->path)->isEqual($from)) {
                $move->path []= $to;
            } else {
                throw new CheckersIllegalMoveException('You must continue your captures with the same piece');
            }
        } else {
            $move = new Move($lastMove->number + 1, array($from, $to));
        }
        
        // Check if there is a piece on from square
        if ($pieceFrom->isFree()) {
            throw new CheckersIllegalMoveException('there is no piece in from: '.$from);
        }
        
        // Check if piece moved is not owned by the other player
        if (($playerPieces - 1) != $checkersParty->getCurrentPlayer()) {
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
        
        // Check if destination square is not already occupied
        if (!$pieceTo->isFree()) {
            throw new CheckersIllegalMoveException('you cannot move on a not empty square');
        }

        // Check for diagonal move
        if (!$from->isSameDiagonal($to)) {
            throw new CheckersIllegalMoveException('you must move diagonnaly');
        }
        
        // Jump distance
        $squareJump = $from->distanceToLine($to);
        
        if (1 === $squareJump) {
            
            // Check if we are in multiple capture phase and player is stopping to capture with the piece
            if ($move->multipleCapture) {
                throw new CheckersIllegalMoveException('You must continue your captures');
            }

            // Piece made a simple move. Check force capture
            if ($variant->getForceCapture()) {
                $capturesAnticipator = new CapturesAnticipator();
                $captures = $capturesAnticipator->anticipate($checkersParty);

                if (count($captures) > 0) {
                    throw new CheckersIllegalMoveException('you must capture opponent piece');
                }
            }
        }
        
        if (!$pieceFrom->isKing()) {
            // If piece is not a king
            
            // Piece jump more than 2 squares
            if ($squareJump > 2) {
                throw new CheckersIllegalMoveException('you can move over one square, or jump over opponent pieces');
            }
            
            if (1 === $squareJump) {
            
                // Check if piece goes forward
                if (($playerPieces === Piece::BLACK) xor (($to->line - $from->line) > 0)) {
                    throw new CheckersIllegalMoveException('you cannot move back');
                }
            }
            
            // Piece seems to jump a piece
            if (2 === $squareJump) {
                
                // Check if piece goes forward
                if (($playerPieces === Piece::BLACK) xor (($to->line - $from->line) > 0)) {
                    if (!$variant->getBackwardCapture()) {
                        throw new CheckersIllegalMoveException('you cannot backward jump in this variant');
                    }
                }
                
                $middle = $from->middle($to);
                $pieceMiddle = $this->pieceAt($grid, $middle);
                
                // Jump an empty square
                if ($pieceMiddle->isFree()) {
                    throw new CheckersIllegalMoveException('you must move over one square');
                }
                
                // Jump an owned piece
                if ($pieceMiddle->getColor() === $playerPieces) {
                    throw new CheckersIllegalMoveException('you cannot jump over your pieces');
                }
                
                // Check if we are jumping a king while variant disallows
                if ($pieceMiddle->isKing() && !$variant->getMenJumpKing()) {
                    throw new CheckersIllegalMoveException('you cannot jump over kings in this variant');
                }
                
                // Remove jumped piece
                $this->pieceAt($grid, $middle, Piece::FREE);
                $move->jumpedPieces []= $middle;
            }
        } else {
            
            // long range king move
            if ($variant->getLongRangeKing()) {
                
                // long range king
                $inters = $from->straightPath($to);
                $pieceMiddle = null;
                $middle = null;
                
                foreach ($inters as $c) {
                    $c = $this->pieceAt($grid, $inter);
                    
                    if (!$p->isFree()) {
                        if (null === $pieceMiddle) {
                            $pieceMiddle = $p;
                            $middle = $c;
                        } else {
                            throw new CheckersIllegalMoveException('you cannot jump two pieces at time');
                        }
                    }
                }
                
                if (null !== $pieceMiddle) {
                    if ($pieceMiddle->getColor() === $playerPieces) {
                        throw new CheckersIllegalMoveException('you cannot jump your own pieces');
                    } else {
                        $this->pieceAt($grid, $middle, Piece::FREE);
                        $move->jumpedPieces []= $middle;
                    }
                }
            } else {
                
                // normal range king move
                $squareJump = $from->distanceToLine($to);
                
                if ($squareJump > 2) {
                    throw new CheckersIllegalMoveException('you cannot make a long range jump in this variant');
                }
                
                if (2 === $squareJump) {
                    $middle = $from->middle($to);
                    $pieceMiddle = $this->pieceAt($grid, $middle);

                    // Jump an owned piece
                    if ($pieceMiddle->getColor() === $playerPieces) {
                        throw new CheckersIllegalMoveException('you cannot jump over your pieces');
                    }

                    // Remove jumped piece
                    $this->pieceAt($grid, $middle, Piece::FREE);
                    $move->jumpedPieces []= $middle;
                }
            }
        }
        
        // Perform move
        $this->pieceAt($grid, $to, $pieceFrom);
        $this->pieceAt($grid, $from, Piece::FREE);
        
        // Update grid
        $checkersParty->setGrid($grid);
        
        // Change player if player cannot jump again
        if (count($move->jumpedPieces) > 0) {
            $capturesAnticipator = new CapturesAnticipator();
            $captures = $capturesAnticipator->anticipate($checkersParty, end($move->path));
            
            if (0 === count($captures)) {
                $move->multipleCapture = false;
                $checkersParty->changeCurrentPlayer();
            } else {
                $move->multipleCapture = true;
            }
        } else {
            $checkersParty->changeCurrentPlayer();
        }
        
        // Update last move
        $checkersParty->setLastMove(json_encode($move));
        
        // Check for promotion
        if (
                (($playerPieces === Piece::WHITE) && ($to->line === 0)) ||
                (($playerPieces === Piece::BLACK) && ($to->line === ($boardSize - 1)))
        ) {
            if (!$move->multipleCapture || $variant->getKingPassing()) {
                $this->promoteAt($grid, $to);
                $checkersParty->setGrid($grid);
            }
        }
        
        return $move;
    }
    
    /**
     * Return piece at coords on grid.
     * If $set is set, update grid.
     * 
     * @param array $grid
     * @param \EL\CoreBundle\Util\Coords $coords
     * @param integer|Coords $set
     * 
     * @return \EL\CheckersBundle\Checkers\Piece
     */
    public function pieceAt(array &$grid, Coords $coords, $set = null)
    {
        if (null === $set) {
            return new Piece($grid[$coords->line][$coords->col]);
        } else {
            $code = ($set instanceof Piece) ? $set->code : $set ;
            return new Piece($grid[$coords->line][$coords->col] = $code);
        }
    }
    
    /**
     * Promote a piece at $coords on $grid
     * 
     * @param array $grid
     * @param \EL\CoreBundle\Util\Coords $coords
     * 
     * @return \EL\CheckersBundle\Checkers\Piece
     * 
     * @throws \Exception
     */
    public function promoteAt(array &$grid, Coords $coords)
    {
        $piece = $this->pieceAt($grid, $coords);
        
        if ($piece->isFree()) {
            throw new \Exception('No piece at '.$coords);
        }
        
        if ($piece->isKing()) {
            throw new \Exception('Piece at '.$coords.' is already promoted');
        }
        
        return $this->pieceAt($grid, $coords, $piece->promote());
    }
}
