<?php

namespace EL\ELCoreBundle\Model;


class ELCoreException extends \Exception
{
    public function __construct($message) {
        parent::__construct($message);
    }
}