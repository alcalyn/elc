<?php

namespace EL\CoreBundle\Exception;

class ELCoreException extends \Exception
{
    const LOGIN_PSEUDO_NOT_FOUND        = 101;
    const LOGIN_PASSWORD_INVALID        = 102;
    const LOGIN_PSEUDO_UNAVAILABLE      = 103;
    const LOGIN_ALREADY_LOGGED          = 104;
    
    public function __construct($message, $code = -1)
    {
        parent::__construct($message, $code);
    }
}
