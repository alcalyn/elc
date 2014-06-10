<?php

namespace EL\CheckersBundle\Checkers;

use EL\CoreBundle\Util\Coords;
use EL\CheckersBundle\Checkers\CheckersException;
use EL\CheckersBundle\Checkers\Variant;
use EL\CheckersBundle\Checkers\Move;
use EL\CheckersBundle\Checkers\Piece;

class CapturesEvaluator
{
    /**
     * Best moves
     * 
     * @var array
     */
    public $captures = array();
    
    /**
     * Number of pieces
     * 
     * @var int
     */
    public $quantity = 0;
    
    /**
     * True if captures are made by a king
     * 
     * @var boolean
     */
    public $preference = false;
    
    /**
     * Number of kings captured
     * 
     * @var int
     */
    public $quality = 0;
    
    /**
     * First king position, 127 if no king
     * 
     * @var int
     */
    public $precedence = 127;
    
    /**
     * Variant used to evaluate
     * 
     * @var Variant
     */
    private $variant = null;
    
    /**
     * Board used to evaluate
     * 
     * @var array
     */
    private $grid = null;
    
    /**
     * From wikipedia, italian rules
     * 
     * If multiple capture sequences are available,
     * one must select the sequence that captures the most pieces.          // Quantity
     * If more than one sequence qualifies,
     * one must capture with a king instead of a man.                       // Preference
     * If more than one sequence qualifies,
     * one must select the sequence that captures the most number of kings. // Quality
     * If there are still more sequences,
     * one must select the sequence that captures a king first.             // Precedence
     * 
     * ===
     * 
     * Evaluate a capture. If better or equals to the best already stored, store it and return true.
     * If less than better to the best already stored, do nothing and return false.
     * 
     * @param \EL\CheckersBundle\Checkers\Move $capture
     * @param \EL\CheckersBundle\Checkers\Variant $variant
     * @param array $grid
     * @param boolean $store set false to just evaluate without store
     * 
     * @return boolean|string true if capture is better or equal, or name of criteria which has failed
     */
    public function evaluate(Move $capture, Variant $variant, array $grid, $store = true)
    {
        $this->variant = $variant;
        $this->grid = $grid;
        
        if ($variant->getForceCaptureQuantity()) {
            $captureQuantity = $capture->getCapturesQuantity();

            if ($captureQuantity !== $this->quantity) {
                if ($captureQuantity > $this->quantity) {
                    $store && $this->foundBetter($capture, $grid);
                    return true;
                } else {
                    return 'quantity';
                }
            }
        }

        if ($variant->getForceCapturePreference()) {
            $capturePreference = $this->pieceAt($grid, $capture->path[0])->isKing();

            if ($capturePreference xor $this->preference) {
                if ($capturePreference && !$this->preference) {
                    $store && $this->foundBetter($capture, $grid);
                    return true;
                } else {
                    return 'preference';
                }
            }
        }

        if ($variant->getForceCaptureQuality()) {
            $captureQuality = $capture->getCapturesQuality($grid);

            if ($captureQuality !== $this->quality) {
                if ($captureQuality > $this->quality) {
                    $store && $this->foundBetter($capture, $grid);
                    return true;
                } else {
                    return 'quality';
                }
            }
        }

        if ($variant->getForceCaptureKingOrder()) {
            $capturePrecedence = $capture->getFirstKingPosition($grid);

            if ($capturePrecedence !== $this->precedence) {
                if ($capturePrecedence < $this->precedence) {
                    $store && $this->foundBetter($capture, $grid);
                    return true;
                } else {
                    return 'precedence';
                }
            }
        }

        $store && ($this->captures []= $capture);
        return true;
    }
    
    /**
     * Evaluate an array of Move
     * 
     * @param array $captures array of Move
     * @param \EL\CheckersBundle\Checkers\Variant $variant
     * @param array $grid
     */
    public function evaluateAll(array $captures, Variant $variant, array $grid)
    {
        $this->variant = $variant;
        $this->grid = $grid;
        
        foreach ($captures as $capture) {
            $this->evaluate($capture, $variant, $grid);
        }
    }
    
    /**
     * Get best captures
     * 
     * @return array of Move
     */
    public function getBestCaptures()
    {
        return $this->captures;
    }
    
    /**
     * Check if $move is beginning with same path as one of best captures
     * 
     * @param \EL\CheckersBundle\Checkers\Move $move
     * 
     * @return boolean|string true if it is a best capture, or name of criteria which has failed
     */
    public function isOneOfBestCapture(Move $move)
    {
        if ((null === $this->variant) || (null === $this->grid)) {
            throw new CheckersException('must evaluate before call '.__METHOD__);
        }
        
        foreach ($this->captures as $capture) {
            if ($move->isBeginningWithSamePath($capture)) {
                return true;
            }
        }
        
        return $this->evaluate($move, $this->variant, $this->grid);
    }
    
    /**
     * Update new best move criteria, and store best capture
     * 
     * @param \EL\CheckersBundle\Checkers\Move $capture
     * @param array $grid
     */
    private function foundBetter(Move $capture, array $grid)
    {
        $this->captures     = array($capture);
        $this->quantity     = $capture->getCapturesQuantity();
        $this->preference   = $this->pieceAt($grid, $capture->path[0])->isKing();
        $this->quality      = $capture->getCapturesQuality($grid);
        $this->precedence   = $capture->getFirstKingPosition($grid);
    }
    
    /**
     * Load from an array of best captures, update criteria
     * 
     * @param array $captures
     * @param \EL\CheckersBundle\Checkers\Variant $variant
     * @param array $grid
     * 
     * @return \self
     * 
     * @throws CheckersException if $captures is empty
     */
    public static function loadFromBestCaptures(array $captures, Variant $variant, array $grid)
    {
        $capturesEvaluator = new self();
        
        foreach ($captures as $capture) {
            $capturesEvaluator->captures []= Move::jsonDeserialize($capture);
        }
        
        if (0 === count($captures)) {
            throw new CheckersException('captures is empty');
        }
        
        $capturesEvaluator->evaluate($capturesEvaluator->captures[0], $variant, $grid, false);
        
        return $capturesEvaluator;
    }
    
    /**
     * Return piece at coords on grid.
     * 
     * @param array $grid
     * @param \EL\CoreBundle\Util\Coords $coords
     * 
     * @return \EL\CheckersBundle\Checkers\Piece
     */
    public function pieceAt(array &$grid, Coords $coords)
    {
        return new Piece($grid[$coords->line][$coords->col]);
    }
}
