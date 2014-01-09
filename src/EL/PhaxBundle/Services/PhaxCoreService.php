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
        
        $phax_reaction = $this
                ->container
                ->get($service_name)
                ->{$phax_action->getAction().'Action'}($phax_action)
        ;
        
        if (!($phax_reaction instanceof PhaxReaction)) {
            throw new PhaxException(
                    'The controller '.$phax_action->getController().'::'.$phax_action->getAction().
                    ' must return an instance of EL\PhaxBundle\Model\PhaxReaction, '.
                    get_class($phax_reaction).' returned'
            );
        }
        
        return $phax_reaction;
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