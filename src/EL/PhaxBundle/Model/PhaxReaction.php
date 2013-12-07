<?php

namespace EL\PhaxBundle\Model;

use EL\PhaxBundle\Model\PhaxException;


class PhaxReaction
{
    
    /**
     *
     * @var array
     *      Contains Phax information :
     *          has_error           : if an error occured
     *          errors              : array containings strings error
     *          trigger_js_reaction : if client should trigger reaction in js
     *                                  with the same name (controller.action())
     * 
     */
    private $metadata;
    
    /**
     * Contains data defined by user
     * 
     * @var array
     */
    private $data;
    
    
    
    
    public function __construct(array $data = array())
    {
        $this->metadata = self::createMetadata();
        $this->data = $data;
    }
    
    
    private static function createMetadata()
    {
        return array(
            'has_error'             => false,
            'errors'                => array(),
            'trigger_js_reaction'   => true,
        );
    }
    
    public function __set($name, $value) {
        if ($name === 'phax_metadata') {
            throw new PhaxException('Cannot use "phax_metadata" as variable name for a PhaxReaction');
        }
        
        $this->data[$name] = $value;
    }
    
    public function __get($name) {
        return $this->data[$name];
    }
    
    public function __isset($name) {
        return isset($this->data[$name]);
    }
    
    public function getJsonData()
    {
        return array(
            'phax_metadata' => $this->metadata,
        ) + $this->data;
    }
    
    public function addError($msg)
    {
        $this->metadata['has_error'] = true;
        $this->metadata['errors'][] = $msg;
    }
    
    public function cleanErrors()
    {
        $this->metadata['has_error'] = false;
        $this->metadata['errors'] = array();
    }
    
    public function enableJsReaction()
    {
        $this->metadata['trigger_js_reaction'] = true;
    }
    
    public function disableJsReaction()
    {
        $this->metadata['trigger_js_reaction'] = false;
    }
    
}