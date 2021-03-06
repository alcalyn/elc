<?php

namespace EL\CheckersBundle\Checkers;

use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use EL\CoreBundle\Util\BitwiseValue;
use EL\CoreBundle\Exception\ELCoreException;

/**
 * @method Variant set($criteria, $boolean)
 * 
 * @Assert\Callback(methods={"validation"})
 */
class Variant extends BitwiseValue implements \JsonSerializable
{
    const PERSONALIZED  = 'personalized';
    const ENGLISH       = 'english';
    const FRENCH        = 'french';
    const ITALIAN       = 'italian';
    const CANADIAN      = 'canadian';
    const RUSSIAN       = 'russian';
    const GERMAN        = 'german';
    const SPANISH       = 'spanish';
    const NETHERLANDS   = 'netherlands';
    
    private static $BOARD_SIZE        = 31;
    private static $SQUARE_USED       = 32;
    private static $RIGHT_SQUARE      = 64;
    private static $BACKWARD_CAPTURE  = 128;
    private static $LONG_RANGE_KING   = 256;
    private static $MEN_JUMP_KING     = 512;
    private static $KING_PASSING      = 1024;
    private static $BLOW_UP           = 2048;
    private static $FIRST_PLAYER      = 4096;
    private static $FORCE_CAPTURE     = 8192;
    private static $CAPT_QUANTITY     = 16384;
    private static $CAPT_QUALITY      = 32768;
    private static $CAPT_KING_ORDER   = 65536;
    private static $CAPT_PREFERENCE   = 131072;
    private static $KING_STOPS_BEHIND = 262144;
    
    
    /**
     * Create a new
     * 
     * @return Variant
     */
    public static function createNewVariant()
    {
        return new Variant();
    }
    
    /**
     * Set boardSize
     *
     * @param integer $boardSize
     * @return Variant
     */
    public function setBoardSize($boardSize)
    {
        if ($boardSize > self::$BOARD_SIZE) {
            throw new ELCoreException('Board size must be <= '.self::$BOARD_SIZE.', got '.$boardSize);
        }
        
        if ($boardSize < 0) {
            throw new ELCoreException('Board size must be >= 0, got '.$boardSize);
        }
        
        $this->value = $this->value & (~self::$BOARD_SIZE);
        $this->value = $this->value | $boardSize;
        
        return $this;
    }

    /**
     * Get boardSize
     *
     * @return integer 
     */
    public function getBoardSize()
    {
        return $this->value & self::$BOARD_SIZE;
    }

    /**
     * Set squareUsed
     *
     * @param boolean $squareUsed
     * @return Variant
     */
    public function setSquareUsed($squareUsed)
    {
        return $this->set(self::$SQUARE_USED, $squareUsed);
    }

    /**
     * Get squareUsed
     *
     * @return boolean 
     */
    public function getSquareUsed()
    {
        return $this->get(self::$SQUARE_USED);
    }

    /**
     * Set rightSquare
     *
     * @param boolean $rightSquare
     * @return Variant
     */
    public function setRightSquare($rightSquare)
    {
        return $this->set(self::$RIGHT_SQUARE, $rightSquare);
    }

    /**
     * Get rightSquare
     *
     * @return boolean 
     */
    public function getRightSquare()
    {
        return $this->get(self::$RIGHT_SQUARE);
    }

    /**
     * Set backwardCapture
     *
     * @param boolean $backwardCapture
     * @return Variant
     */
    public function setBackwardCapture($backwardCapture)
    {
        return $this->set(self::$BACKWARD_CAPTURE, $backwardCapture);
    }

    /**
     * Get backwardCapture
     *
     * @return boolean 
     */
    public function getBackwardCapture()
    {
        return $this->get(self::$BACKWARD_CAPTURE);
    }

    /**
     * Set longRangeKing
     *
     * @param boolean $longRangeKing
     * @return Variant
     */
    public function setLongRangeKing($longRangeKing)
    {
        return $this->set(self::$LONG_RANGE_KING, $longRangeKing);
    }

    /**
     * Get longRangeKing
     *
     * @return boolean 
     */
    public function getLongRangeKing()
    {
        return $this->get(self::$LONG_RANGE_KING);
    }

    /**
     * Set menJumpKing
     *
     * @param boolean $menJumpKing
     * @return Variant
     */
    public function setMenJumpKing($menJumpKing)
    {
        return $this->set(self::$MEN_JUMP_KING, $menJumpKing);
    }

    /**
     * Get menJumpKing
     *
     * @return boolean 
     */
    public function getMenJumpKing()
    {
        return $this->get(self::$MEN_JUMP_KING);
    }

    /**
     * Set kingPassing
     *
     * @param boolean $kingPassing
     * @return Variant
     */
    public function setKingPassing($kingPassing)
    {
        return $this->set(self::$KING_PASSING, $kingPassing);
    }

    /**
     * Get kingPassing
     *
     * @return boolean 
     */
    public function getKingPassing()
    {
        return $this->get(self::$KING_PASSING);
    }

    /**
     * Set blowUp
     *
     * @param boolean $blowUp
     * @return Variant
     */
    public function setBlowUp($blowUp)
    {
        return $this->set(self::$BLOW_UP, $blowUp);
    }

    /**
     * Get blowUp
     *
     * @return boolean 
     */
    public function getBlowUp()
    {
        return $this->get(self::$BLOW_UP);
    }

    /**
     * Set firstPlayer
     *
     * @param boolean $firstPlayer
     * @return Variant
     */
    public function setFirstPlayer($firstPlayer)
    {
        return $this->set(self::$FIRST_PLAYER, $firstPlayer);
    }

    /**
     * Get firstPlayer
     *
     * @return boolean 
     */
    public function getFirstPlayer()
    {
        return $this->get(self::$FIRST_PLAYER);
    }

    /**
     * Set forceCapture
     *
     * @param boolean $forceCapture
     * @return Variant
     */
    public function setForceCapture($forceCapture)
    {
        return $this->set(self::$FORCE_CAPTURE, $forceCapture);
    }

    /**
     * Get forceCapture
     *
     * @return boolean 
     */
    public function getForceCapture()
    {
        return $this->get(self::$FORCE_CAPTURE);
    }

    /**
     * Set forceCapture by quantity
     *
     * @param boolean $forceCapture
     * @return Variant
     */
    public function setForceCaptureQuantity($forceCapture)
    {
        return $this->set(self::$CAPT_QUANTITY, $forceCapture);
    }

    /**
     * Get forceCapture by quantity
     *
     * @return boolean 
     */
    public function getForceCaptureQuantity()
    {
        return $this->get(self::$CAPT_QUANTITY);
    }

    /**
     * Set forceCapture by quality
     *
     * @param boolean $forceCapture
     * @return Variant
     */
    public function setForceCaptureQuality($forceCapture)
    {
        return $this->set(self::$CAPT_QUALITY, $forceCapture);
    }

    /**
     * Get forceCapture by quality
     *
     * @return boolean 
     */
    public function getForceCaptureQuality()
    {
        return $this->get(self::$CAPT_QUALITY);
    }

    /**
     * Set forceCapture by KingOrder
     *
     * @param boolean $forceCapture
     * @return Variant
     */
    public function setForceCaptureKingOrder($forceCapture)
    {
        return $this->set(self::$CAPT_KING_ORDER, $forceCapture);
    }

    /**
     * Get forceCapture by KingOrder
     *
     * @return boolean 
     */
    public function getForceCaptureKingOrder()
    {
        return $this->get(self::$CAPT_KING_ORDER);
    }

    /**
     * Set forceCapture by Preference
     *
     * @param boolean $forceCapture
     * @return Variant
     */
    public function setForceCapturePreference($forceCapture)
    {
        return $this->set(self::$CAPT_PREFERENCE, $forceCapture);
    }

    /**
     * Get forceCapture by Preference
     *
     * @return boolean 
     */
    public function getForceCapturePreference()
    {
        return $this->get(self::$CAPT_PREFERENCE);
    }

    /**
     * Set king Stops Behind
     *
     * @param boolean $kingStopsBehind
     * @return Variant
     */
    public function setKingStopsBehind($kingStopsBehind)
    {
        return $this->set(self::$KING_STOPS_BEHIND, $kingStopsBehind);
    }

    /**
     * Get king Stops Behind
     *
     * @return boolean
     */
    public function getKingStopsBehind()
    {
        return $this->get(self::$KING_STOPS_BEHIND);
    }
    
    public function validation(ExecutionContextInterface $context)
    {
        if (!in_array($this->getBoardSize(), array(4, 6, 8, 10, 12, 14))) {
            $context->addViolationAt('boardSize', 'board.size.invalid');
        }
    }
    
    /**
     * Implements JsonSerializable
     * 
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'binaryValue'   => $this->getBinaryValue(),
        );
    }
}
