<?php

namespace EL\CoreBundle\Form\Entity;

use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use EL\CoreBundle\Entity\Party as CoreParty;

class Options
{
    /**
     * @var CoreParty
     */
    private $coreParty;
    
    /**
     * @var stdClass
     */
    private $extendedOptions;
    
    
    public function __construct(CoreParty $coreParty, $extendedOptions)
    {
        $this->coreParty        = $coreParty;
        $this->extendedOptions  = $extendedOptions;
    }
    
    public function getCoreParty()
    {
        return $this->coreParty;
    }

    public function setCoreParty(CoreParty $coreParty)
    {
        $this->coreParty = $coreParty;
        
        return $this;
    }

    public function getExtendedOptions()
    {
        return $this->extendedOptions;
    }

    public function setExtendedOptions($extendedOptions)
    {
        $this->extendedOptions = $extendedOptions;
        
        return $this;
    }
}
