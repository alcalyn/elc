<?php

namespace EL\CheckersBundle\Checkers;

use EL\CoreBundle\Util\BitwiseValue;
use EL\CoreBundle\Exception\ELCoreException;

/**
 * @method Variant set($criteria, $boolean)
 */
class Variant extends BitwiseValue implements \JsonSerializable
{
    const PERSONALIZED  = 'personalized';
    const ENGLISH       = 'english';
    const FRENCH        = 'french';
    
    private static $BOARD_SIZE        = 31;
    private static $SQUARE_USED       = 32;
    private static $RIGHT_SQUARE      = 64;
    private static $BACKWARD_CAPTURE  = 128;
    private static $LONG_RANGE_KING   = 256;
    private static $MEN_JUMP_KING     = 512;
    private static $KING_PASSING      = 1024;
    private static $BLOW_UP           = 2048;
    private static $LET_DO            = 4096;
    private static $FIRST_PLAYER      = 8192;
    private static $FORCE_CAPTURE     = 16384;
    private static $CAPT_QUANTITY     = 32768;
    private static $CAPT_QUALITY      = 65536;
    private static $CAPT_KING_ORDER   = 131072;
    private static $CAPT_PREFERENCE   = 262144;
    
    
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
     * @return CheckersParty
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
     * @return CheckersParty
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
     * @return CheckersParty
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
     * @return CheckersParty
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
     * @return CheckersParty
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
     * @return CheckersParty
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
     * @return CheckersParty
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
     * @return CheckersParty
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
     * Set letDo
     *
     * @param boolean $letDo
     * @return CheckersParty
     */
    public function setLetDo($letDo)
    {
        return $this->set(self::$LET_DO, $letDo);
    }

    /**
     * Get letDo
     *
     * @return boolean 
     */
    public function getLetDo()
    {
        return $this->get(self::$LET_DO);
    }

    /**
     * Set firstPlayer
     *
     * @param boolean $firstPlayer
     * @return CheckersParty
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
     * @return CheckersParty
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
     * @return CheckersParty
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
     * @return CheckersParty
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
     * @return CheckersParty
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
     * @return CheckersParty
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
