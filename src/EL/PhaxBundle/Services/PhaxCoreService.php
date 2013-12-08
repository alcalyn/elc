<?php

namespace EL\PhaxBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;
use EL\PhaxBundle\Model\PhaxException;
use EL\PhaxBundle\Model\PhaxReaction;


class PhaxCoreService extends ContainerAware
{
    
    /**
     * @param string $controller_name
     * @param string $action_name
     * @param array $params
     * @return \EL\PhaxBundle\Model\PhaxReaction
     * @throws PhaxException if :
     *                  - controller does not exists (not declared as service "phax.XXX"
     *                  - controller does not return a PhaxReaction instance
     */
    public function action($controller_name, $action_name = null, array $params = array())
    {
        if (null === $action_name) {
            $action_name = 'default';
        }
        
        $service_name = 'phax.'.$controller_name;
        
        if (!$this->container->has($service_name)) {
            throw new PhaxException(
                    'The controller '.$controller_name.' does not exists. '.
                    'It must be declared as service named '.$service_name
            );
        }
        
        $phax_reaction = $this
                ->container
                ->get($service_name)
                ->{$action_name.'Action'}($params);
        
        if (!($phax_reaction instanceof PhaxReaction)) {
            throw new PhaxException(
                    'The controller '.$controller_name.'::'.$action_name.' must return an instance of EL\PhaxBundle\Model\PhaxReaction, '.
                    get_class($phax_reaction).' returned'
            );
        }
        
        return $phax_reaction;
    }
}