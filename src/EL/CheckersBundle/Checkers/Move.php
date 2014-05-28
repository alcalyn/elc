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
     * Constructor
     * 
     * @param array $path
     * @param array $jumpedPieces
     */
    public function __construct($number, array $path = array(), array $jumpedPieces = array())
    {
        $this->number = $number;
        $this->path = $path;
        $this->jumpedPieces = $jumpedPieces;
    }
    
    /**
     * Implementation of JsonSerializable
     */
    public function jsonSerialize()
    {
        return array(
            'number'        => $this->number,
            'path'          => $this->path,
            'jumpedPieces'  => $this->jumpedPieces,
        );
    }
    
    /**
     * Deserialize a move serial
     * 
     * @param array $serial
     * 
     * @return \EL\CheckersBundle\Checkers\Move
     */
    public static function jsonDeserialize($serial)
    {
        if (null === $serial) {
            return new Move(0);
        } else {
            return new Move(
                    $serial->number,
                    $serial->path,
                    $serial->jumpedPieces
            );
        }
    }
    
    public function __clone()
    {
        return new self($this->number, $this->path, $this->jumpedPieces);
    }
}
