<?php

namespace EL\CoreBundle\Exception;

class ELCoreException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
