<?php

namespace EL\CheckersBundle\Services;

use EL\CoreBundle\Exception\ELCoreException;
use EL\CheckersBundle\Checkers\Variant;
use EL\CheckersBundle\Checkers\Piece;

class Checkers
{
    const WHITE = true;
    const BLACK = false;
    
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
}
