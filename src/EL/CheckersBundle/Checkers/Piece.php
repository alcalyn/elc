<?php

namespace EL\CheckersBundle\Checkers;

class Piece
{
    const FREE          = 0;
    const WHITE         = 1;
    const BLACK         = 2;
    const WHITE_KING    = 3;
    const BLACK_KING    = 4;
    
    public $code;
    
    public function __construct($code)
    {
        $this->code = $code;
    }
    
    public function isFree()
    {
        return 0 === $this->code;
    }
    
    public function isKing()
    {
        return $this->code > 2;
    }
    
    public function promote()
    {
        $this->code += 2;
        
        return $this;
    }
    
    public function getColor()
    {
        if ($this->isFree()) {
            return Piece::FREE;
        }
        
        return (($this->code % 2) === 1) ? Piece::WHITE : Piece::BLACK;
    }
}
