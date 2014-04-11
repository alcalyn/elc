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
    
    /**
     * Return an array representing the grid such as:
     * 
     *  Array(
     *      [0] => Array(
     *          [seeds] => Array(
     *              [0] => 2
     *              [1] => 6
     *              ...
     *              [5] => 3
     *          )
     *          [attic] => 12
     *      )
     *      [1] => Array(
     *          ...
     *      )
     *  )
     * 
     * @param string $grid
     * 
     * @return array
     */
    public function unserializeGrid($grid)
    {
        $rows = explode('|', $grid);
        
        $row0 = explode(';', $rows[0]);
        $row1 = explode(';', $rows[1]);
        
        return array(
            array(
                'seeds' => explode(',', $row0[0]),
                'attic' => $row0[1],
            ),
            array(
                'seeds' => explode(',', $row1[0]),
                'attic' => $row1[1],
            ),
        );
    }
    
    /**
     * @param array $grid
     * 
     * @return string
     */
    public function serializeGrid(array $grid)
    {
        $row0 = implode(',', $grid[0]['seeds']) . ';' . $grid[0]['attic'] ;
        $row1 = implode(',', $grid[1]['seeds']) . ';' . $grid[1]['attic'] ;
        
        return $row0.'|'.$row1;
    }
}
