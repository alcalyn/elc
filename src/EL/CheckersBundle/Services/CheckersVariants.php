<?php

namespace EL\CheckersBundle\Services;

use EL\CheckersBundle\Checkers\Variant;

class CheckersVariants
{
    /**
     * Array containing variants
     * 
     * @var Variant[]
     */
    private $variants;
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->initVariants();
    }
    
    /**
     * Get predefined Variants
     * 
     * @return Variant[] of predefined Variant
     */
    public function getVariants()
    {
        return $this->variants;
    }
    
    /**
     * @param string $name
     * @return Variant
     */
    public function getVariant($name)
    {
        return $this->variants[$name];
    }
    
    /**
     * Create predefined variants
     * 
     * @return array of Variant
     */
    private function initVariants()
    {
        $this->variants = array();

        // English
        $this->variants[Variant::ENGLISH] = Variant
                ::createNewVariant()
                ->setBoardSize(8)
                ->setSquareUsed(Checkers::BLACK)
                ->setRightSquare(Checkers::WHITE)
                ->setBackwardCapture(false)
                ->setLongRangeKing(false)
                ->setMenJumpKing(true)
                ->setKingPassing(false)
                ->setForceCapture(true)
                ->setBlowUp(false)
                ->setFirstPlayer(Checkers::BLACK)
        ;

        // French / International
        $this->variants[Variant::FRENCH] = Variant
                ::createNewVariant()
                ->setBoardSize(10)
                ->setSquareUsed(Checkers::BLACK)
                ->setRightSquare(Checkers::WHITE)
                ->setBackwardCapture(true)
                ->setLongRangeKing(true)
                ->setMenJumpKing(true)
                ->setKingPassing(false)
                ->setForceCapture(true)
                ->setForceCaptureQuantity(true)
                ->setBlowUp(false)
                ->setFirstPlayer(Checkers::WHITE)
        ;

        // Italian
        $this->variants[Variant::ITALIAN] = Variant
                ::createNewVariant()
                ->setBoardSize(8)
                ->setSquareUsed(Checkers::BLACK)
                ->setRightSquare(Checkers::WHITE)
                ->setBackwardCapture(false)
                ->setLongRangeKing(false)
                ->setMenJumpKing(false)
                ->setKingPassing(false)
                ->setForceCapture(true)
                ->setForceCaptureQuantity(true)
                ->setForceCaptureQuality(true)
                ->setForceCaptureKingOrder(true)
                ->setForceCapturePreference(true)
                ->setBlowUp(false)
                ->setFirstPlayer(Checkers::WHITE)
        ;

        // Canadian
        $this->variants[Variant::CANADIAN] = Variant
                ::createNewVariant()
                ->setBoardSize(12)
                ->setSquareUsed(Checkers::BLACK)
                ->setRightSquare(Checkers::WHITE)
                ->setBackwardCapture(true)
                ->setLongRangeKing(true)
                ->setMenJumpKing(true)
                ->setKingPassing(false)
                ->setForceCapture(true)
                ->setForceCaptureQuantity(true)
                ->setBlowUp(false)
                ->setFirstPlayer(Checkers::WHITE)
        ;

        // Russian
        $this->variants[Variant::RUSSIAN] = Variant
                ::createNewVariant()
                ->setBoardSize(10)
                ->setSquareUsed(Checkers::BLACK)
                ->setRightSquare(Checkers::WHITE)
                ->setBackwardCapture(true)
                ->setLongRangeKing(true)
                ->setMenJumpKing(true)
                ->setKingPassing(true)
                ->setForceCapture(true)
                ->setBlowUp(false)
                ->setFirstPlayer(Checkers::WHITE)
        ;

        // German
        $this->variants[Variant::GERMAN] = Variant
                ::createNewVariant()
                ->setBoardSize(8)
                ->setSquareUsed(Checkers::WHITE)
                ->setRightSquare(Checkers::WHITE)
                ->setBackwardCapture(false)
                ->setLongRangeKing(true)
                ->setMenJumpKing(true)
                ->setKingPassing(false)
                ->setForceCapture(true)
                ->setBlowUp(false)
                ->setKingStopsBehind(true)
                ->setFirstPlayer(Checkers::WHITE)
        ;

        // Spanish
        $this->variants[Variant::SPANISH] = Variant
                ::createNewVariant()
                ->setBoardSize(8)
                ->setSquareUsed(Checkers::WHITE)
                ->setRightSquare(Checkers::WHITE)
                ->setBackwardCapture(false)
                ->setLongRangeKing(true)
                ->setMenJumpKing(true)
                ->setKingPassing(false)
                ->setForceCapture(true)
                ->setForceCaptureQuantity(true)
                ->setForceCaptureQuality(true)
                ->setForceCaptureKingOrder(false)
                ->setForceCapturePreference(false)
                ->setBlowUp(false)
                ->setFirstPlayer(Checkers::WHITE)
        ;

        // Netherlands
        $this->variants[Variant::NETHERLANDS] = Variant
                ::createNewVariant()
                ->setBoardSize(10)
                ->setSquareUsed(Checkers::BLACK)
                ->setRightSquare(Checkers::WHITE)
                ->setBackwardCapture(true)
                ->setLongRangeKing(true)
                ->setMenJumpKing(true)
                ->setKingPassing(false)
                ->setForceCapture(true)
                ->setForceCaptureQuantity(false)
                ->setBlowUp(false)
                ->setFirstPlayer(Checkers::WHITE)
        ;
    }
}
