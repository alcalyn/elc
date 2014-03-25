<?php

namespace EL\CoreBundle\Extensions;

class ELTwigExtensions extends \Twig_Extension
{
    private $requestStack;
    
    public function __construct($requestStack)
    {
        $this->requestStack = $requestStack;
    }
    
    public function getFilters()
    {
        return array(
            'onpath' => new \Twig_Filter_Method($this, 'onpath'),
        );
    }
    
    public function getFunctions()
    {
        return array(
        );
    }
    
    public function onpath()
    {
        $current_route = $this->requestStack->getMasterRequest()->get('_route');
        
        $routes = func_get_args();
        $string = array_shift($routes);
        
        foreach ($routes as $route) {
            if ($route == $current_route) {
                return $string;
            }
        }
        
        return '';
    }
    
    public function getName()
    {
        return 'el_twig_extensions';
    }
}
