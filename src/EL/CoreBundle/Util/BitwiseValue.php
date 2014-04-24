<?php

namespace EL\CoreBundle\Util;

class BitwiseValue
{
    /**
     * Integer containing configuration at bit level
     * 
     * @var integer
     */
    protected $value;
    
    /**
     * Constructor
     * 
     * @param integer $value
     */
    public function __construct($value = 0)
    {
        $this->setBinaryValue($value);
    }
    
    /**
     * @param integer $criteria
     * @param boolean $boolean
     * 
     * @return BitwiseValue
     */
    public function set($criteria, $boolean)
    {
        if ($boolean) {
            $this->value = $this->value | $criteria;
        } else {
            $this->value = $this->value & (~$criteria);
        }
        
        return $this;
    }
    
    /**
     * @param integer $criteria
     * 
     * @return boolean
     */
    public function get($criteria)
    {
        return ($this->value & $criteria) > 0;
    }
    
    /**
     * @param integer $value
     * 
     * @return BitwiseValue
     */
    public function setBinaryValue($value)
    {
        $this->value = $value;
        
        return $this;
    }
    
    /**
     * @return integer
     */
    public function getBinaryValue()
    {
        return $this->value;
    }
}
