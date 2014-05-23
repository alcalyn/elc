<?php

namespace EL\CoreBundle\Util;

class Coords
{
    /**
     * @var integer
     */
    public $line;
    
    /**
     * @var integer
     */
    public $col;
    
    /**
     * Constructor.
     * 
     * @param integer $line
     * @param integer $col
     */
    public function __construct($line = 0, $col = 0)
    {
        $this->line = intval($line);
        $this->col  = intval($col);
    }
    
    /**
     * Check if this Coords exists in a board of size $sive
     * 
     * @param integer $size
     * 
     * @return boolean
     */
    public function isInsideBoard($size)
    {
        return
            $this->line >= 0 &&
            $this->line < $size &&
            $this->col >= 0 &&
            $this->col < $size
        ;
    }
    
    /**
     * Return true if coords are in same line to $to
     * 
     * @param \EL\CoreBundle\Util\Coords $to
     * @return boolean
     */
    public function isSameLine(Coords $to)
    {
        return $this->line === $to->line;
    }
    
    /**
     * Return true if coords are in same col to $to
     * 
     * @param \EL\CoreBundle\Util\Coords $to
     * @return boolean
     */
    public function isSameCol(Coords $to)
    {
        return $this->col === $to->col;
    }
    
    /**
     * Check if coords are odd
     * 
     * @return boolean
     */
    public function isOdd()
    {
        return 1 === (($this->line + $this->col) % 2);
    }
    
    /**
     * Check if coords are even
     * 
     * @return boolean
     */
    public function isEven()
    {
        return 0 === (($this->line + $this->col) % 2);
    }
    
    /**
     * Return line distance to $to
     * 
     * @param \EL\CoreBundle\Util\Coords $to
     * 
     * @return integer
     */
    public function distanceToLine(Coords $to)
    {
        return abs($to->line - $this->line);
    }
    
    /**
     * Return col distance to $to
     * 
     * @param \EL\CoreBundle\Util\Coords $to
     * 
     * @return integer
     */
    public function distanceToCol(Coords $to)
    {
        return abs($to->col - $this->col);
    }
    
    /**
     * Return Manhattan distance to $to
     * 
     * @param \EL\CoreBundle\Util\Coords $to
     * 
     * @return integer
     */
    public function distanceManhattan(Coords $to)
    {
        return $this->distanceToLine($to) + $this->distanceToCol($to);
    }
    
    /**
     * Return true if coords are in same diagonal
     * 
     * @param \EL\CoreBundle\Util\Coords $to
     * @return boolean
     */
    public function isDiagonal(Coords $to)
    {
        return $this->distanceToLine($to) === $this->distanceToCol($to);
    }
    
    /**
     * Test equality of two Coords
     * 
     * @param Coords $to
     * 
     * @return boolean
     */
    public function isEqual(Coords $to)
    {
        return $this->isSameLine($to) && $this->isSameCol($to);
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return "Coords ( $this->line ; $this->col )\n";
    }
}
