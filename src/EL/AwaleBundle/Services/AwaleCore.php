<?php

namespace EL\AwaleBundle\Services;

class AwaleCore
{
    /**
     * Return a filled grid of awale
     * under the form:
     * 
     *      x,x,x,x,x,x;X|y,y,y,y,y,y;Y
     * 
     * x seeds of player 1
     * X seeds in attic of player 1
     * y seeds of player 2
     * Y seeds in attic of player 2
     * 
     * @param integer $seedsPerContainer
     * @return string
     */
    public function fillGrid($seedsPerContainer)
    {
        $row = implode(',', array_fill(0, 6, $seedsPerContainer));
        $row .= ';0';
        
        return $row.'|'.$row;
    }
}
