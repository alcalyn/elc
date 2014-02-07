<?php

namespace EL\ELCoreBundle\Services;

class WidgetService
{
    
    /**
     * @var array of widget to display
     */
    private $widgets;
    
    
    
    public function __construct()
    {
        $this->widgets = array();
        $this->init();
    }
    
    private function init()
    {
        $this->add('ELCoreBundle:Widget:myParties');
    }
    
    /**
     * Add a widget to display
     * 
     * @param \EL\ELCoreBundle\Model\WidgetInterface $widget
     */
    public function add($controller, array $parameters = array())
    {
        $this->widgets []= array(
            'controller'    => $controller,
            'parameters'    => $parameters,
        );
    }
    
    /**
     * Return all widgets
     * 
     * @return array of WidgetInterface
     */
    public function getAll()
    {
        return $this->widgets;
    }
}
