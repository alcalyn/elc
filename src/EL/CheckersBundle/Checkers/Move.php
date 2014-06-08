<?php

namespace EL\CheckersBundle\Checkers;

use EL\CoreBundle\Util\Coords;
use EL\CheckersBundle\Checkers\Piece;

class Move implements \JsonSerializable
{
    /**
     * @var integer
     */
    public $number;
    
    /**
     * @var array of Coords
     */
    public $path;
    
    /**
     * @var array of Coords where pieces were
     */
    public $jumpedPieces;
    
    /**
     * @var boolean if we are in a multiple capture phase
     */
    public $multipleCapture;
    
    /**
     * Constructor
     * 
     * @param array $path
     * @param array $jumpedPieces
     */
    public function __construct($number, array $path = array(), array $jumpedPieces = array(), $multipleCapture = false)
    {
        $this->number = $number;
        $this->path = $path;
        $this->jumpedPieces = $jumpedPieces;
        $this->multipleCapture = $multipleCapture;
    }
    
    /**
     * Return the count of captured quantity
     * 
     * @return int
     */
    public function getCapturesQuantity()
    {
        return count($this->jumpedPieces);
    }
    
    /**
     * Return the count of kings in capture sequence
     * 
     * @param array $grid board
     * 
     * @return int
     */
    public function getCapturesQuality(array $grid)
    {
        $kingNumber = 0;
        
        foreach ($this->jumpedPieces as $c) {
            if ($grid[$c->line][$c->col] > 2) {
                $kingNumber++;
            }
        }
        
        return $kingNumber;
    }
    
    /**
     * Return the position of the first king, or -1 if no king in captures
     * 
     * @param array $grid board
     * 
     * @return int
     */
    public function getFirstKingPosition(array $grid)
    {
        $position = 0;
        
        foreach ($this->jumpedPieces as $c) {
            if ($grid[$c->line][$c->col] > 2) {
                return $position;
            } else {
                $position++;
            }
        }
        
        return -1;
    }
    
    /**
     * Check if this move and $move begins with the same path
     * 
     * @param Move $move
     * 
     * @return boolean
     */
    public function isBeginningWithSamePath(Move $move)
    {
        $minPath = min(count($this->path), count($move->path));
        
        for ($i = 0; $i < $minPath; $i++) {
            if (!$this->path[$i]->isEqual($move->path[$i])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Implementation of JsonSerializable
     */
    public function jsonSerialize()
    {
        return array(
            'number'            => $this->number,
            'path'              => $this->path,
            'jumpedPieces'      => $this->jumpedPieces,
            'multipleCapture'   => $this->multipleCapture,
        );
    }
    
    /**
     * Deserialize a move serial
     * 
     * @param array $serial
     * 
     * @return Move
     */
    public static function jsonDeserialize($serial)
    {
        if (null === $serial) {
            return new Move(0);
        } else {
            $move = new Move($serial->number);
            
            foreach ($serial->path as $coord) {
                $move->path []= new Coords($coord->line, $coord->col);
            }
            
            foreach ($serial->jumpedPieces as $coord) {
                $move->jumpedPieces []= new Coords($coord->line, $coord->col);
            }
            
            $move->multipleCapture = $serial->multipleCapture;
            
            return $move;
        }
    }
    
    public function __clone()
    {
        return new self($this->number, $this->path, $this->jumpedPieces, $this->multipleCapture);
    }
}
