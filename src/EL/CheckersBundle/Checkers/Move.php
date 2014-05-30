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
