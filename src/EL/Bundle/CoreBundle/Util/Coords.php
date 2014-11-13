<?php

namespace EL\Bundle\CoreBundle\Util;

class Coords implements \JsonSerializable
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
     * @param \EL\Bundle\CoreBundle\Util\Coords $to
     * @return boolean
     */
    public function isSameLine(Coords $to)
    {
        return $this->line === $to->line;
    }
    
    /**
     * Return true if coords are in same col to $to
     * 
     * @param \EL\Bundle\CoreBundle\Util\Coords $to
     * @return boolean
     */
    public function isSameCol(Coords $to)
    {
        return $this->col === $to->col;
    }
    
    /**
     * Return true if coords are in same diagonal
     * 
     * @param \EL\Bundle\CoreBundle\Util\Coords $to
     * @return boolean
     */
    public function isSameDiagonal(Coords $to)
    {
        return $this->distanceToLine($to) === $this->distanceToCol($to);
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
     * Add coords to this and return new Coords
     * 
     * @param \EL\Bundle\CoreBundle\Util\Coords $c
     * 
     * @return \EL\Bundle\CoreBundle\Util\Coords
     */
    public function add(Coords $c)
    {
        return new Coords($this->line + $c->line, $this->col + $c->col);
    }
    
    /**
     * Substract coords to this and return new Coords
     * 
     * @param \EL\Bundle\CoreBundle\Util\Coords $c
     * 
     * @return \EL\Bundle\CoreBundle\Util\Coords
     */
    public function sub(Coords $c)
    {
        return new Coords($this->line - $c->line, $this->col - $c->col);
    }
    
    /**
     * Multiply $n times coords to this and return new Coords
     * 
     * @param integer $n
     * 
     * @return \EL\Bundle\CoreBundle\Util\Coords
     */
    public function mul($n)
    {
        return new Coords($this->line * $n, $this->col * $n);
    }
    
    /**
     * Divide line and col by $n and return new Coords
     * 
     * @param integer $n
     * 
     * @return \EL\Bundle\CoreBundle\Util\Coords
     */
    public function div($n)
    {
        return new Coords($this->line / $n, $this->col / $n);
    }
    
    /**
     * Return the discret middle Coords between this and $to
     * 
     * @param \EL\Bundle\CoreBundle\Util\Coords $to
     * 
     * @return \EL\Bundle\CoreBundle\Util\Coords, or null if there is no discret middle
     */
    public function middle(Coords $to)
    {
        $sub = $to->sub($this);
        
        if (($sub->line % 2) + ($sub->col % 2)) {
            return null;
        } else {
            return $this->add($to)->div(2);
        }
    }
    
    /**
     * Give the direction to $to, mul by $coef
     * 
     * @param Coords $to
     * @param integer $coef
     * @return Coords or null if to is not in diagonal, line or col
     */
    public function direction(Coords $to, $coef = 1)
    {
        $add = $to->sub($this);
        
        if ((0 === $add->line) || (0 === $add->col) || (abs($add->line) === abs($add->col))) {
            if ($add->line > 0) {
                $add->line = $coef;
            } elseif ($add->line < 0) {
                $add->line = -$coef;
            }
            
            if ($add->col > 0) {
                $add->col = $coef;
            } elseif ($add->col < 0) {
                $add->col = -$coef;
            }
            
            return $add;
        } else {
            return null;
        }
    }
    
    /**
     * Return all coords between $this and $to.
     * Must be same diagonal, line or column
     * 
     * @param \EL\Bundle\CoreBundle\Util\Coords $to
     * 
     * @return array of Coords
     */
    public function straightPath(Coords $to)
    {
        $iterator = $this->direction($to);
        
        if (null !== $iterator) {
            $path = array();
            
            foreach ($this->iterateCoords($iterator) as $c) {
                if ($c->isEqual($to)) {
                    return $path;
                } else {
                    $path []= $c;
                }
            }
        } else {
            return null;
        }
    }
    
    /**
     * Return a generator which iterate every Coords between this
     * and the board side iterated by $iterator
     * 
     * @param \EL\Bundle\CoreBundle\Util\Coords $iterator
     * @param integer $boardSize or null to iterate limitless
     * 
     * @return \Generator
     */
    public function iterateCoords(Coords $iterator, $boardSize = null)
    {
        $c = $this->add($iterator);
        
        while ((null === $boardSize) || ($c->isInsideBoard($boardSize))) {
            yield $c;
            $c = $c->add($iterator);
        }
    }
    
    /**
     * Return line distance to $to
     * 
     * @param \EL\Bundle\CoreBundle\Util\Coords $to
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
     * @param \EL\Bundle\CoreBundle\Util\Coords $to
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
     * @param \EL\Bundle\CoreBundle\Util\Coords $to
     * 
     * @return integer
     */
    public function distanceManhattan(Coords $to)
    {
        return $this->distanceToLine($to) + $this->distanceToCol($to);
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
     * Clone
     * 
     * @return Coords
     */
    public function __clone()
    {
        return new self($this->line, $this->col);
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return "Coords ( $this->line ; $this->col )";
    }
    
    /**
     * Implementation of JsonSerializable
     */
    public function jsonSerialize()
    {
        return array(
            'line'  => $this->line,
            'col'   => $this->col,
        );
    }
}
