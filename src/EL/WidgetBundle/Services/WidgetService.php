<?php

namespace EL\WidgetBundle\Services;

class WidgetService
{
    /**
     * @var array of widget to display
     */
    private $widgets;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->widgets = array();
        $this->init();
    }
    
    /**
     * Add global widgets
     */
    private function init()
    {
        $this->add('WidgetBundle:Widget:myParties');
    }
    
    /**
     * Add a widget to display
     * 
     * @param string $controller name
     * @param array $parameters
     */
    public function add($controller, array $parameters = array(), $position = null)
    {
        $array = array(
            'controller'    => $controller,
            'parameters'    => $parameters,
        );
        
        if (null === $position) {
            $this->widgets []= $array;
        } else {
            array_splice($this->widgets, $position, 0, array($array));
        }
    }
    
    /**
     * Return all widgets
     * 
     * @return array
     */
    public function getAll()
    {
        return $this->widgets;
    }
}
