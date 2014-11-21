<?php

namespace EL\Core\Exception;

class LoginException extends UserException
{
    /**
     * @var integer
     */
    const LOGIN_PSEUDO_NOT_FOUND = 101;
    
    /**
     * @var integer
     */
    const LOGIN_PASSWORD_INVALID = 102;
    
    /**
     * @var integer
     */
    const LOGIN_PSEUDO_UNAVAILABLE = 103;
    
    /**
     * @var integer
     */
    const LOGIN_ALREADY_LOGGED = 104;
    
    /**
     * @param string $message
     * @param integer $code
     */
    public function __construct($message, $code)
    {
        parent::__construct($message, UserException::TYPE_WARNING, $code);
    }
}
