<?php

namespace EL\CheckersBundle\Services;

use EL\CoreBundle\Util\Coords;
use EL\CheckersBundle\Checkers\CheckersException;
use EL\CheckersBundle\Checkers\CheckersIllegalMoveException;
use EL\CheckersBundle\Checkers\Variant;
use EL\CheckersBundle\Checkers\Piece;
use EL\CheckersBundle\Checkers\Move;
use EL\CheckersBundle\Entity\CheckersParty;
use EL\CheckersBundle\Checkers\CapturesAnticipatorCache;
use EL\CheckersBundle\Checkers\CapturesEvaluator;
use EL\CheckersBundle\Services\CheckersVariants;

class Checkers
{
    const WHITE = false;
    const BLACK = true;
    
    /**
     * @var CheckersVariants 
     */
    private $checkersVariants;
    
    /**
     * Constructor.
     */
    public function __construct(CheckersVariants $checkersVariants)
    {
        $this->checkersVariants = $checkersVariants;
    }
    
    /**
     * @return array of predefined Variants
     */
    public function getVariants()
    {
        return $this->checkersVariants->getVariants();
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
     * @throws CheckersException if $boardSize not in 4, 6, 8, 10, 12, 14
     */
    public function initGrid(Variant $checkersVariant)
    {
        $boardSize = $checkersVariant->getBoardSize();
        
        if (!in_array($boardSize, array(4, 6, 8, 10, 12, 14))) {
            throw new CheckersException('$boardSize must be in 4, 6, 8, 10, 12, 14, got "'.$boardSize.'"');
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
        $middle         = null;
        $pieceMiddle    = null;
        
        // Create new current move, or keep the last if we are in a multiple capture phase
        if ($lastMove->multipleCapture) {
            $move = $lastMove;
            if (end($move->path)->isEqual($from)) {
                $move->path []= $to;
            } else {
                throw new CheckersIllegalMoveException('illegalmove.continue.capture');
            }
        } else {
            $move = new Move($lastMove->number + 1, array($from, $to));
        }
        
        // Check if there is a piece on from square
        if ($pieceFrom->isFree()) {
            throw new CheckersIllegalMoveException('illegalmove.no.piece.%name%.%coords%', array(
                '%name%'    => 'from',
                '%coords%'  => $from,
            ));
        }
        
        // Check if piece moved is not owned by the other player
        if (($playerPieces - 1) != $checkersParty->getCurrentPlayer()) {
            throw new CheckersIllegalMoveException('illegalmove.cannot.move.opponent.pieces');
        }
        
        // Test if from and to are the same
        if ($from->isEqual($to)) {
            throw new CheckersIllegalMoveException('illegalmove.no.move.detected');
        }
        
        // Check if movement is inside board
        if (!$from->isInsideBoard($boardSize)) {
            throw new CheckersIllegalMoveException('illegalmove.%name%.must.be.inboard.%boardsize%.%coords%', array(
                '%name%'        => 'from',
                '%boardsize%'   => $boardSize,
                '%coords%'      => $from,
            ));
        }
        
        // Check if we move piece inside the board
        if (!$to->isInsideBoard($boardSize)) {
            throw new CheckersIllegalMoveException('illegalmove.%name%.must.be.inboard.%boardsize%.%coords%', array(
                '%name%'        => 'to',
                '%boardsize%'   => $boardSize,
                '%coords%'      => $to,
            ));
        }
        
        // Check if destination square is not already occupied
        if (!$pieceTo->isFree()) {
            throw new CheckersIllegalMoveException('illegalmove.destination.occupied');
        }

        // Check for diagonal move
        if (!$from->isSameDiagonal($to)) {
            throw new CheckersIllegalMoveException('illegalmove.must.move.diagonally', array(), 'simplemove.jpg');
        }
        
        // Prepare a capture anticipator instance
        $capturesAnticipator = new CapturesAnticipatorCache();
        
        // Jump distance
        $squareJump = $from->distanceToLine($to);
        
        if (!$pieceFrom->isKing()) {
            // If piece is not a king
            
            // Piece jump more than 2 squares
            if ($squareJump > 2) {
                throw new CheckersIllegalMoveException('illegalmove.cannot.move.too.far', array(), 'move-or-jump.jpg');
            }
            
            if (1 === $squareJump) {
            
                // Check if piece goes forward
                if (($playerPieces === Piece::BLACK) xor (($to->line - $from->line) > 0)) {
                    throw new CheckersIllegalMoveException('illegalmove.cannot.move.back');
                }
            }
            
            // Piece seems to jump a piece
            if (2 === $squareJump) {
                
                // Check if piece goes forward
                if (($playerPieces === Piece::BLACK) xor (($to->line - $from->line) > 0)) {
                    if (!$variant->getBackwardCapture()) {
                        throw new CheckersIllegalMoveException('illegalmove.cannot.backward.jump');
                    }
                }
                
                $middle = $from->middle($to);
                $pieceMiddle = $this->pieceAt($grid, $middle);
                
                // Jump an empty square
                if ($pieceMiddle->isFree()) {
                    throw new CheckersIllegalMoveException(
                            'illegalmove.cannot.move.too.far',
                            array(),
                            'move-or-jump.jpg'
                    );
                }
                
                // Jump an owned piece
                if ($pieceMiddle->getColor() === $playerPieces) {
                    throw new CheckersIllegalMoveException(
                            'illegalmove.cannot.jump.own.pieces',
                            array(),
                            'jump-own-piece.jpg'
                    );
                }
                
                // Check if we are jumping a king while variant disallows
                if ($pieceMiddle->isKing() && !$variant->getMenJumpKing()) {
                    throw new CheckersIllegalMoveException('illegalmove.cannot.jump.kings.variant');
                }
            }
        } else {
            
            // long range king move
            if ($variant->getLongRangeKing()) {
                
                // long range king
                $path = $from->straightPath($to);
                $pieceMiddle = null;
                $middle = null;
                
                foreach ($path as $c) {
                    $p = $this->pieceAt($grid, $c);
                    
                    if (!$p->isFree()) {
                        if (null === $pieceMiddle) {
                            if ($p->getColor() === $playerPieces) {
                                throw new CheckersIllegalMoveException(
                                        'illegalmove.cannot.jump.own.pieces',
                                        array(),
                                        'jump-own-piece.jpg'
                                );
                            } else {
                                $pieceMiddle = $p;
                                $middle = $c;
                            }
                        } else {
                            throw new CheckersIllegalMoveException('illegalmove.cannot.jump.two.pieces');
                        }
                    } elseif ((null === $pieceMiddle) && $variant->getKingStopsBehind()) {
                        throw new CheckersIllegalMoveException('illegalmove.king.must.stop.behind');
                    }
                }
            } else {
                
                // normal range king move
                $squareJump = $from->distanceToLine($to);
                
                if ($squareJump > 2) {
                    throw new CheckersIllegalMoveException('illegalmove.no.long.range.king');
                }
                
                if (2 === $squareJump) {
                    $middle = $from->middle($to);
                    $pieceMiddle = $this->pieceAt($grid, $middle);

                    // Jump an owned piece
                    if ($pieceMiddle->getColor() === $playerPieces) {
                        throw new CheckersIllegalMoveException(
                                'illegalmove.cannot.jump.own.pieces',
                                array(),
                                'jump-own-piece.jpg'
                        );
                    }
                    
                    // Jump an empty piece
                    if ($pieceMiddle->isFree()) {
                        throw new CheckersIllegalMoveException('illegalmove.no.long.range.king');
                    }
                }
            }
        }
        
        // No captures, check if we were in multiple capture, or force capture
        if (null === $middle) {
            if ($move->multipleCapture) {
                throw new CheckersIllegalMoveException('illegalmove.continue.capture');
            }
            
            if ($variant->getForceCapture()) {
                $captures = $capturesAnticipator->anticipate($checkersParty);

                if (count($captures) > 0) {
                    throw new CheckersIllegalMoveException('illegalmove.must.capture');
                }
            }
        }
        
        // Update $move, add jumpedPiece if there is one
        if (null !== $middle) {
            $move->jumpedCoords []= $middle;
        }
        
        // Check captures rules if there is a capture and a rule
        if (null !== $middle) {
            if (
                    $variant->getForceCaptureQuantity() ||
                    $variant->getForceCaptureQuality() ||
                    $variant->getForceCaptureKingOrder() ||
                    $variant->getForceCapturePreference()
            ) {
                if ($move->multipleCapture) {
                    // Player continues captures, check if he follows one of the best already stored
                    $bestCaptures = $checkersParty->getBestMultipleCaptures();
                    
                    if (null !== $bestCaptures) {
                        $capturesEvaluator = CapturesEvaluator::loadFromBestCaptures(
                                json_decode($bestCaptures),
                                $variant,
                                $grid
                        );
                        
                        $isOneOfBest = $capturesEvaluator->isOneOfBestCapture($move);
                        
                        if (true !== $isOneOfBest) {
                            throw new CheckersIllegalMoveException('illegalmove.capturebetter.'.$isOneOfBest);
                        }
                    }
                } else {
                    // Player starts captures, store all best possible captures, and check if move is on the good way
                    $captures = $capturesAnticipator->anticipate($checkersParty);
                    
                    if (count($captures) > 1) {
                        $capturesEvaluator = new CapturesEvaluator();
                        $capturesEvaluator->evaluateAll($captures, $variant, $grid);
                        $bestCaptures = $capturesEvaluator->getBestCaptures();
                        $isOneOfBest = $capturesEvaluator->isOneOfBestCapture($move);
                        
                        if (true === $isOneOfBest) {
                            $checkersParty->setBestMultipleCaptures(json_encode($bestCaptures));
                        } else {
                            throw new CheckersIllegalMoveException('illegalmove.capturebetter.'.$isOneOfBest);
                        }
                    }
                }
            }
        }
        
        // Check huffs if variant allows to and no pieces has been jumped
        if ($variant->getBlowUp()) {
            if (null === $middle) {
                $huff = $this->calculateHuff($checkersParty, $capturesAnticipator, $move);
            } else {
                $huff = self::createHuffInstance();
            }
            
            $checkersParty->setHuff(json_encode($huff));
        }
        
        // Perform move
        $this->pieceAt($grid, $to, $pieceFrom);
        $this->pieceAt($grid, $from, Piece::FREE);
        
        // Remove jumped piece if there is one
        if (null !== $middle) {
            $this->pieceAt($grid, $middle, Piece::FREE);
        }
        
        // Update grid
        $checkersParty->setGrid($grid);
        
        // Change player if player cannot jump again
        if (count($move->jumpedCoords) > 0) {
            $captures = $capturesAnticipator->anticipate($checkersParty, $to);
            
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
        if (!$pieceFrom->isKing()) {
            if (
                    (($playerPieces === Piece::WHITE) && ($to->line === 0)) ||
                    (($playerPieces === Piece::BLACK) && ($to->line === ($boardSize - 1)))
            ) {
                if (!$move->multipleCapture || $variant->getKingPassing()) {
                    $this->promoteAt($grid, $to);
                    $checkersParty->setGrid($grid);
                }
            }
        }
        
        return $move;
    }
    
    /**
     * A player tries to huff a piece
     * 
     * @param \EL\CheckersBundle\Entity\CheckersParty $checkersParty
     * @param \EL\CoreBundle\Util\Coords $coords where the piece to huff is
     */
    public function huff(CheckersParty $checkersParty, Coords $coords)
    {
        $variant        = new Variant($checkersParty->getParameters());
        $grid           = $checkersParty->getGrid();
        $piece          = $this->pieceAt($grid, $coords);
        $lastMove       = Move::jsonDeserialize(json_decode($checkersParty->getLastMove()));
        
        // Check if variant allows huff
        if (!$variant->getBlowUp()) {
            throw new CheckersIllegalMoveException('illegalmove.nohuff.variant');
        }
        
        // Check if there is a piece on huff coords
        if ($piece->isFree()) {
            throw new CheckersIllegalMoveException('illegalmove.no.piece.%name%.%coords%', array(
                '%name%'    => 'huff',
                '%coords%'  => $coords,
            ));
        }
        
        // Check if player tries to huff his own pieces
        if ($piece->getColor() === ($checkersParty->getCurrentPlayer() ? Piece::BLACK : Piece::WHITE)) {
            throw new CheckersIllegalMoveException('illegalmove.cannot.huff.own.pieces');
        }
        
        // Check if there is no captures in last move
        if (count($lastMove->jumpedCoords) > 0) {
            throw new CheckersIllegalMoveException('illegalmove.cannot.huff.already.jumped');
        }
        
        $huff = json_decode($checkersParty->getHuff());
        
        if (null === $huff) {
            throw new CheckersIllegalMoveException('illegalmove.cannot.huff.this.piece');
        }
        
        if (null !== $huff->huffed) {
            throw new CheckersIllegalMoveException('illegalmove.cannot.huff.already.huffed');
        }
        
        $isHuffable = false;
        
        foreach ($huff->huffCoords as $huffCoord) {
            if (($coords->line === $huffCoord->line) && ($coords->col === $huffCoord->col)) {
                $isHuffable = true;
                break;
            }
        }
        
        if (!$isHuffable) {
            throw new CheckersIllegalMoveException('illegalmove.cannot.huff.this.piece');
        }
        
        $huff->huffed = $coords;
        $this->pieceAt($grid, $coords, Piece::FREE);
        
        $checkersParty
                ->setHuff(json_encode($huff))
                ->setGrid($grid)
        ;
    }
    
    /**
     * Save all coords which can be huffed by opponent
     * 
     * @param \EL\CheckersBundle\Entity\CheckersParty $checkersParty
     * @param \EL\CheckersBundle\Checkers\CapturesAnticipatorCache $capturesAnticipator
     * @param \EL\CheckersBundle\Checkers\Move $move
     * @return \stdClass huff
     */
    private function calculateHuff(
            CheckersParty $checkersParty,
            CapturesAnticipatorCache $capturesAnticipator = null,
            Move $move = null)
    {
        if (null === $capturesAnticipator) {
            $capturesAnticipator = new CapturesAnticipatorCache();
        }
        
        $huff = self::createHuffInstance();
        
        $captures = $capturesAnticipator->anticipate($checkersParty);
        
        // Save coords which can be huffed
        foreach ($captures as $capture) {
            $alreadyIn = false;

            foreach ($huff->huffCoords as $huffCoord) {
                if ($huffCoord->isEqual($capture->path[0])) {
                    $alreadyIn = true;
                    break;
                }
            }

            if (!$alreadyIn) {
                $huff->huffCoords[] = $capture->path[0];
            }
        }
        
        // Update coords if piece just moved
        if (null !== $move) {
            foreach ($huff->huffCoords as &$huffCoord) {
                if ($huffCoord->isEqual($move->path[0])) {
                    $huffCoord = $move->path[1];
                }
            }
        }
        
        return $huff;
    }
    
    /**
     * Create a new instance of huff
     * 
     * @return \stdClass
     */
    private static function createHuffInstance()
    {
        $huff = new \stdClass();
        $huff->huffed       = null;
        $huff->huffCoords   = array();
        
        return $huff;
    }
    
    /**
     * Check if there is a winner or party
     * 
     * @param array $grid
     * @param boolean $player
     * 
     * @return mixed
     *          false   : white wins
     *          true    : black wins
     *          null    : party not ended
     */
    public function hasWinner(array $grid)
    {
        $boardSize = count($grid);
        
        // Check if both players still have pieces
        $has = array(
            self::WHITE => false,
            self::BLACK => false,
        );
        
        for ($i = 0; $i < $boardSize; $i++) {
            for ($j = 0; $j < $boardSize; $j++) {
                $p = $grid[$i][$j];
                
                if ($p > 0) {
                    $color = (bool) ($p % 2);
                    
                    $has[!$color] = true;
                    
                    if ($has[$color]) {
                        return null;
                    }
                }
            }
        }
        
        return $has[self::BLACK];
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
    
    public function remake(CheckersParty $oldParty)
    {
        $newParty   = new CheckersParty();
        $variant    = new Variant($oldParty->getParameters());
        
        return $newParty
                ->setParameters($oldParty->getParameters())
                ->setGrid($this->initGrid($variant))
                ->setCurrentPlayer($variant->getFirstPlayer())
        ;
    }
}
