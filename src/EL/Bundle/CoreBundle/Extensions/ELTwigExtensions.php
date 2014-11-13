<?php

namespace EL\Bundle\CoreBundle\Extensions;

use EL\Bundle\CoreBundle\Entity\Score;

class ELTwigExtensions extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;
    
    /**
     * Constructor.
     * 
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct($requestStack)
    {
        $this->requestStack = $requestStack;
    }
    
    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            'onpath'        => new \Twig_Filter_Method($this, 'onpath'),
            'displayScore'  => new \Twig_Filter_Method($this, 'displayScore'),
        );
    }
    
    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
        );
    }
    
    /**
     * Output the string if we are in one of route of arguments.
     * 
     * Example:
     *      onpath('string', 'route_name', 'another_route_name', ...);
     * 
     * output 'string' if we are in route_name or another_route_name, or ...
     * else return empty string.
     * 
     * @return string
     */
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
    
    /**
     * Return displayable score value $scoreKey of $score
     * 
     * Round elo, ratio...
     * 
     * @return string
     */
    public function displayScore(Score $score, $scoreKey)
    {
        switch ($scoreKey) {
            case 'ratio':
                return round($score->getRatio(), 3);
            
            case 'elo':
                return round($score->getElo());
            
            case 'eloReliability':
                return round($score->getEloReliability() * 100).' %';
            
            default:
                return $score->{'get'.$scoreKey}();
        }
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'el_twig_extensions';
    }
}
