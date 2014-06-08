<?php

namespace EL\CheckersBundle\Checkers;

use EL\CoreBundle\Util\Coords;
use EL\CheckersBundle\Entity\CheckersParty;

class CapturesAnticipatorCache
{
    /**
     *
     * @var CapturesAnticipator
     */
    private $capturesAnticipator;
    
    /**
     * @var array
     */
    private $captures;
    
    /**
     * @var array
     */
    private $capturesFrom;
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->capturesAnticipator = new CapturesAnticipator();
        $this->captures = null;
        $this->capturesFrom = array();
    }
    
    /**
     * Return a cached array of anticipated moves
     * 
     * @param CheckersParty $checkersParty
     * @param Coords $coords (if set, anticipate moves only for the piece on $coords)
     * 
     * @return array
     */
    public function anticipate(CheckersParty $checkersParty, Coords $coords = null)
    {
        if (null === $coords) {
            if (null === $this->captures) {
                $this->captures = $this->capturesAnticipator->anticipate($checkersParty);
            }
            
            return $this->captures;
        } else {
            $index = $coords.'';
            
            if (!isset($this->capturesFrom[$index])) {
                $this->capturesFrom[$index] = $this->capturesAnticipator->anticipate($checkersParty, $coords);
            }
            
            return $this->capturesFrom[$index];
        }
    }
}
