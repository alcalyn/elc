<?php

namespace EL\PhaxBundle\Model;


/**
 * PhaxAction, created at phax request.
 * Contains response data
 */
class PhaxAction implements \JsonSerializable
{
    
    /**
     * Name of the controller
     * 
     * @var string
     */
    private $controller;
    
    /**
     * Name of the action. Will call [action_name]Action()
     * 
     * @var string 
     */
    private $action;
    
    /**
     * Request instance
     * 
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;
    
    /**
     * True if the request is called in command line
     * False if called by ajax query
     * 
     * @var boolean 
     */
    private $is_cli;
    
    /**
     * Contains action parameters
     * 
     * @var array
     */
    private $data;
    
    
    public function __construct($controller, $action = null, $params = array())
    {
        $this->controller   = $controller;
        $this->action       = $action;
        $this->data         = $params;
    }
    
    public function __set($name, $value)
    {
        if ($name === 'phax_metadata') {
            throw new PhaxException(
                    'Cannot use "phax_metadata" as variable name for a PhaxAction'
            );
        }
        
        $this->data[$name] = $value;
    }
    
    public function __get($name)
    {
        return $this->data[$name];
    }
    
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }
    
    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * @return string
     */
    public function getLocale()
    {
    	return $this->getRequest()->getLocale();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \EL\PhaxBundle\Model\PhaxAction
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isCli()
    {
        return $this->is_cli;
    }

    /**
     * @param boolean $is_cli
     * @return \EL\PhaxBundle\Model\PhaxAction
     */
    public function setIsCli($is_cli)
    {
        $this->is_cli = $is_cli;
        return $this;
    }
    
    public function getController()
    {
        return $this->controller;
    }

    public function getAction()
    {
        return $this->action;
    }
    
    public function get($variable, $default = null)
    {
    	return $this->__isset($variable) ?
    		$this->__get($variable) :
    		$default ;
    }

    public function jsonSerialize() {
        $array = $this->data;
        $array['phax_metadata'] = array(
            'controller'    => $this->getController(),
            'action'        => $this->getAction(),
            'is_cli'        => $this->isCli(),
        );
        
        return $array;
    }
}
