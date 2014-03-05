<?php

namespace EL\CoreBundle\Model;

class ELCoreException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
