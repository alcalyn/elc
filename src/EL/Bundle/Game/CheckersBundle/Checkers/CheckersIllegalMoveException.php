<?php

namespace EL\Bundle\Game\CheckersBundle\Checkers;

class CheckersIllegalMoveException extends CheckersException
{
    /**
     * @var array
     */
    private $msgVars;
    
    /**
     *
     * @var string
     */
    private $illustration;
    
    /**
     * @param string $msg message translation id
     * @param array $msgVars parameters of translation
     * @param string $illustration
     */
    public function __construct($msg, array $msgVars = array(), $illustration = null)
    {
        parent::__construct($msg);
        
        $this->msgVars = $msgVars;
        $this->illustration = $illustration;
    }
    
    /**
     * @return array
     */
    public function getMsgVars()
    {
        return $this->msgVars;
    }
    
    /**
     * @return string
     */
    public function getIllustration()
    {
        return $this->illustration;
    }
}
