<?php

namespace EL\Bundle\Game\CheckersBundle\Checkers;

use EL\Bundle\CoreBundle\Util\Coords;
use EL\Bundle\Game\CheckersBundle\Entity\CheckersParty;

class CapturesAnticipatorCache
{
    /**
     *
     * @var MoveAnticipator
     */
    private $moveAnticipator;
    
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
        $this->moveAnticipator = new MoveAnticipator();
        $this->captures = null;
        $this->capturesFrom = array();
    }
    
    /**
     * Return a cached array of anticipated moves
     * 
     * @param CheckersParty $checkersParty
     * @param Coords $coords (if set, anticipate moves only for the piece on $coords)
     * 
     * @return array of Move
     */
    public function anticipate(CheckersParty $checkersParty, Coords $coords = null)
    {
        if (null === $coords) {
            if (null === $this->captures) {
                $this->captures = $this->moveAnticipator->anticipateCaptures($checkersParty);
            }
            
            return $this->captures;
        } else {
            $index = $coords.'';
            
            if (!isset($this->capturesFrom[$index])) {
                $this->capturesFrom[$index] = $this->moveAnticipator->anticipateCaptures($checkersParty, $coords);
            }
            
            return $this->capturesFrom[$index];
        }
    }
}
