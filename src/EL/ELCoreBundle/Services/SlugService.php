<?php

namespace EL\ELCoreBundle\Services;


class SlugService
{
    
    
    public function slug($s, $prefix = '', $sufix = '')
    {
        $r = $s;
        
        $r = strtolower($r);
        $r = str_replace(array(','), '', $r);
        $r = preg_replace("/\s+/", ' ', $r);
        $r = trim($r);
        $r = str_replace(' ', '-', $r);
        
        return $prefix.$r.$sufix;
    }
    
    
    
}
