<?php

namespace EL\CoreBundle\Exception;

use Symfony\Component\HttpFoundation\Session\Session;

class ELUserException extends ELCoreException
{
    
    const TYPE_INFO     = 'info';
    const TYPE_WARNING  = 'warning';
    const TYPE_DANGER   = 'danger';
    
    
    private $type;
    
    
    /**
     * @param string $message
     */
    public function __construct($message, $code = -1, $type = self::TYPE_DANGER)
    {
        parent::__construct($message, $code);
        
        $this->setType($type);
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
    
    public function addFlashMessage(Session $session)
    {
        $session->getFlashBag()->add(
            $this->getType(),
            $this->getMessage()
        );
    }
    
    public function checkType($type)
    {
        if (!in_array($type, array('info', 'warning', 'danger'))) {
            throw new ELCoreException(
                'ELUserException::type can be "info", "warning" or "danger", got "'.$type.'"'
            );
        }
    }
}
