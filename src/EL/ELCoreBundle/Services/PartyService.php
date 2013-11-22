<?php

namespace EL\ELCoreBundle\Services;


class PartyService
{
    
    
    
    
    public function generateRandomTitle()
    {
        return 'Party '.rand(10000, 99999);
    }
}