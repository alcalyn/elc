<?php

namespace EL\ELCoreBundle\Model;

use EL\ElCoreBundle\Form\Type\DefaultOptionsType;
use EL\ElCoreBundle\Form\Entity\DefaultOptions;


class ELDefaultGame implements ELGameInterface
{
    
    public function getOptionsType()
    {
        return new DefaultOptionsType();
    }
}
