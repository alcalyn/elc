<?php

namespace EL\PhaxBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use EL\PhaxBundle\Model\PhaxException;
use EL\PhaxBundle\Model\PhaxAction;
use EL\PhaxBundle\Model\PhaxReaction;


class PhaxCoreService extends ContainerAware
{
    
    /**
     * @param PhaxAction containing request and/or parameters
     * @return \EL\PhaxBundle\Model\PhaxReaction
     * @throws PhaxException if :
     *                  - controller does not exists (not declared as service "phax.XXX"
     *                  - controller does not return a PhaxReaction instance
     */
    public function action(PhaxAction $phax_action)
    {
        $service_name = 'phax.'.$phax_action->getController();
        
        if (!$this->container->has($service_name)) {
            throw new PhaxException(
                    'The controller '.$phax_action->getController().' does not exists. '.
                    'It must be declared as service named '.$service_name
            );
        }
        
        $phax_controller = $this
                ->container
                ->get($service_name)
        ;
        
        $phax_reaction = $this->callAction($phax_controller, $phax_action);
        
        if (!($phax_reaction instanceof PhaxReaction)) {
            throw new PhaxException(
                    'The controller '.$phax_action->getController().'::'.$phax_action->getAction().
                    ' must return an instance of EL\PhaxBundle\Model\PhaxReaction, '.
                    get_class($phax_reaction).' returned'
            );
        }
        
        return $phax_reaction;
    }
    
    
    /**
     * Call action by passing arguments to method parameters with the same name.
     * Or pass PhaxAction if myAction(PhaxAction $arg) is used.
     * 
     * @param mixed $phax_controller instance of a phax controller
     * @param PhaxAction $phax_action instance of PhaxAction
     * @return PhaxReaction
     * @throws PhaxException if method parameters name does not correspond to $phax_action arguments
     */
    private function callAction($phax_controller, $phax_action)
    {
        $method_name    = $phax_action->getAction().'Action';
        $method         = new \ReflectionMethod($phax_controller, $method_name);
        $arguments      = array();
        
        foreach ($method->getParameters() as $parameter) {
            $parameter_class = $parameter->getClass();
            
            if ($parameter_class && $parameter_class->getName() === 'EL\PhaxBundle\Model\PhaxAction') {
                $arguments []= $phax_action;
            } elseif (isset($phax_action->{$parameter->getName()})) {
                $arguments []= $phax_action->{$parameter->getName()};
            } else {
                throw new PhaxException(
                    'Action '.$method_name.' of your Phax controller '.get_class($phax_controller)
                    . ' has a parameter '.$parameter->getName().' which do not correspond to any query parameter.'
                    . ' To pass entire PhaxAction instance, use "myAction(PhaxAction $arg_name)"'
                    . ' in your method declaration.'
                );
            }
        }
        
        return call_user_func_array(array($phax_controller, $method_name), $arguments);
    }
    
    
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $error_message = $event
                ->getException()
                ->getMessage()
        ;
        
        $event->setResponse(new Response($error_message));
    }
}