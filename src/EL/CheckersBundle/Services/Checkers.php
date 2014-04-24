<?php

namespace EL\CheckersBundle\Services;

use EL\CheckersBundle\Util\CheckersVariant;

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
     * @return array of CheckersVariant
     */
    public function getVariants()
    {
        if (null === $this->variants) {
            $this->variants = array();
            
            // English
            $this->variants[CheckersVariant::ENGLISH] = CheckersVariant
                    ::createNewVariant()
                    ->setBoardSize(8)
                    ->setFirstPlayer(self::WHITE)
                    ->setMenJumpKing(true)
            ;
            
            // French
            $this->variants[CheckersVariant::FRENCH] = CheckersVariant
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
     * @param CheckersVariant $checkersVariant
     * 
     * @return string
     */
    public function getVariantName(CheckersVariant $checkersVariant)
    {
        foreach ($this->getVariants() as $name => $variant) {
            if ($checkersVariant->equals($variant)) {
                return $name;
            }
        }
        
        return CheckersVariant::PERSONALIZED;
    }
}
