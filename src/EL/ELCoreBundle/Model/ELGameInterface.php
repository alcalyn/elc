<?php

namespace EL\ELCoreBundle\Model;



interface ELGameInterface
{
    
    /**
     * Return a form type which extends base form
     * at the creation of a personalized party.
     * 
     * @return AbstractForm
     */
    public function getOptionsType();
    
    /**
     * Return an object to hydrate by options form.
     * Can have default values
     */
    public function getOptions();
    
}