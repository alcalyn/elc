<?php

namespace EL\CheckersBundle\Checkers;

class CheckersIllegalMoveException extends CheckersException
{
    /**
     * @var array
     */
    private $msgVars;
    
    /**
     * @param string $msg message translation id
     * @param array $msgVars parameters of translation
     */
    public function __construct($msg, array $msgVars = array())
    {
        parent::__construct($msg);
        
        $this->msgVars = $msgVars;
    }
    
    /**
     * @return array
     */
    public function getMsgVars()
    {
        return $this->msgVars;
    }
}
