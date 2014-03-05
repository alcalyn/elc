<?php

namespace EL\CoreBundle\Services;

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
        $this->add('CoreBundle:Widget:myParties');
    }
    
    /**
     * Add a widget to display
     * 
     * @param \EL\CoreBundle\Model\WidgetInterface $widget
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
