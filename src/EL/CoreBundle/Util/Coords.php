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
     * @return string
     */
    public function __toString()
    {
        return "Coords ( $this->line ; $this->col )\n";
    }
}
