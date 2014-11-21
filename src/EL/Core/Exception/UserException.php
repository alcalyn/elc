<?php

namespace EL\Core\Exception;

class UserException extends Exception
{
    /**
     * @var string
     */
    const TYPE_INFO = 'info';
    
    /**
     * @var string
     */
    const TYPE_WARNING = 'warning';
    
    /**
     * @var string
     */
    const TYPE_DANGER = 'danger';
    
    /**
     * Contains an error level
     * 
     * @var string
     */
    private $type;
    
    /**
     * @param string $message translation key
     * @param string $type error level
     * @param integer $code
     */
    public function __construct($message, $type = self::TYPE_DANGER, $code = -1)
    {
        parent::__construct($message, $code);
        
        $this->checkType($type);
        $this->setType($type);
    }
    
    /**
     * Check if $type is a valid error level
     * 
     * @param string $type
     * 
     * @throws Exception
     */
    private function checkType($type)
    {
        $availableTypes = array(
            self::TYPE_INFO,
            self::TYPE_WARNING,
            self::TYPE_DANGER,
        );
        
        if (!in_array($type, $availableTypes)) {
            throw new Exception(
                get_class($this).'::type can be "'.implode('", "', $availableTypes).'", got "'.$type.'"'
            );
        }
    }
    
    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
