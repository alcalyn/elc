<?php

namespace EL\Core\Exception;

class Exception extends \Exception
{
    /**
     * @param string $message
     * @param integer $code
     */
    public function __construct($message, $code = -1)
    {
        parent::__construct($message, $code);
    }
}
