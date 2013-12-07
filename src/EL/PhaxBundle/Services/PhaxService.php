<?php

namespace EL\PhaxBundle\Services;

use EL\PhaxBundle\Model\PhaxReaction;


class PhaxService
{
    
    public function reaction(array $parameters = array())
    {
        return new PhaxReaction($parameters);
    }
    
    public function error($msg)
    {
        $reaction = new PhaxReaction();
        $reaction->addError($msg);
        return $reaction;
    }
    
    public function void()
    {
        $reaction = new PhaxReaction();
        $reaction->disableJsReaction();
        return $reaction;
    }
    
}