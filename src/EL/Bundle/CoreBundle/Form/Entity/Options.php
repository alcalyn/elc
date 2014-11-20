<?php

namespace EL\Bundle\CoreBundle\Form\Entity;

use EL\Core\Entity\Party as CoreParty;

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
