<?php

namespace EL\Bundle\Game\AwaleBundle\Services;

class AwaleCore
{
    const WIN_0     = 0;
    const WIN_1     = 1;
    const DRAW      = -1;
    const NO_WIN    = -2;
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
    
    /**
     * Play a grid naively
     * 
     * @param array   $grid
     * @param integer $player
     * @param integer $move
     * 
     * @return array grid played
     */
    public function play(array $grid, $player, $move)
    {
        // Take seeds in hand
        $hand = $grid[$player]['seeds'][$move];
        $grid[$player]['seeds'][$move] = 0;
        
        $row = $player;
        $box = $move;
        
        /**
         * Dispatch seeds
         */
        while ($hand > 0) {
            if (0 === $row) {
                if (5 === $box) {
                    $row = 1;
                } else {
                    $box++;
                }
            } else {
                if (0 === $box) {
                    $row = 0;
                } else {
                    $box--;
                }
            }
            
            // Feed box
            if (($row !== $player) || ($box !== $move)) {
                $hand--;
                $grid[$row]['seeds'][$box]++;
            }
        }
        
        /**
         * Store opponent seeds
         */
        while (($row !== $player) && in_array($grid[$row]['seeds'][$box], array(2, 3))) {
            // Store his seeds
            $grid[$player]['attic'] += $grid[$row]['seeds'][$box];
            $grid[$row]['seeds'][$box] = 0;
            
            // Check previous box
            if (0 === $row) {
                if (0 === $box) {
                    $row = 1;
                } else {
                    $box--;
                }
            } else {
                if (5 === $box) {
                    $row = 0;
                } else {
                    $box++;
                }
            }
        }
        
        return $grid;
    }
    
    /**
     * Check if there is seeds in row of $array
     * 
     * @param array $grid
     * 
     * @return boolean
     */
    public function hasSeeds(array $grid, $player)
    {
        foreach ($grid[$player]['seeds'] as $box) {
            if ($box > 0) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if $player can do a move which let the opponent play
     * 
     * @param array $grid
     * @param integer $player
     * 
     * @return boolean
     */
    public function canFeedOpponent(array $grid, $player)
    {
        if (0 === $player) {
            for ($i = 0; $i < 6; $i++) {
                if ($grid[0]['seeds'][$i] > (5 - $i)) {
                    return true;
                }
            }
        }
        
        if (1 === $player) {
            for ($i = 0; $i < 6; $i++) {
                if ($grid[1]['seeds'][$i] > $i) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Store remaining seeds when game stop
     * by impossibility to
     * 
     * @param array $grid
     */
    public function storeRemainingSeeds(array $grid)
    {
        for ($i = 0; $i < 6; $i++) {
            $grid[0]['attic'] += $grid[0]['seeds'][$i];
            $grid[0]['seeds'][$i] = 0;
            
            $grid[1]['attic'] += $grid[1]['seeds'][$i];
            $grid[1]['seeds'][$i] = 0;
        }
        
        return $grid;
    }
    
    /**
     * Update last move
     * 
     * @param string  $lastMove
     * @param integer $box
     * @return string
     */
    public function getUpdatedLastMove($lastMove, $box)
    {
        $data = explode('|', $lastMove);
        
        return implode('|', array(
            intval($data[0]) + 1,
            $box,
        ));
    }
    
    /**
     * Check if a player of the grid has won
     * 
     * @param array $grid
     * @param integer $seedsPerContainer
     * 
     * @return integer
     */
    public function hasWinner(array $grid, $seedsPerContainer)
    {
        $seedsToWin = $this->getSeedsNeededToWin($seedsPerContainer);
        
        if ($grid[0]['attic'] > $seedsToWin) {
            return self::WIN_0;
        }
        
        if ($grid[1]['attic'] > $seedsToWin) {
            return self::WIN_1;
        }
        
        if (($seedsToWin === $grid[0]['attic']) && ($seedsToWin === $grid[1]['attic'])) {
            return self::DRAW;
        }
        
        return self::NO_WIN;
    }
    
    /**
     * Return the number the player must exceed to win
     * 
     * @param integer $seedsPerContainer the initial amount of seed in a box
     * 
     * @return integer
     */
    public function getSeedsNeededToWin($seedsPerContainer)
    {
        return $seedsPerContainer * 6;
    }
}
