<?php

namespace EL\PhaxBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use EL\PhaxBundle\Model\PhaxReaction;


/**
 * Helper class for building default phax reaction,
 * such as standard reaction with some parameters,
 * standard phax error, void reaction or simple message.
 * 
 * Call this in your controller using :
 * 
 *      return $this->get('phax')->reaction(array(
 *          ...
 *      ));
 */
class PhaxService extends ContainerAware
{
    
    /**
     * @param array $parameters
     * @return \EL\PhaxBundle\Model\PhaxReaction
     * 
     * Build and return a basic reaction with parameters
     */
    public function reaction(array $parameters = array())
    {
        return new PhaxReaction($parameters);
    }
    
    
    /**
     * Build and return a PhaxReaction or Response
     * depending of query type
     * 
     * @param string $view
     * @param array $parameters
     * @return \EL\PhaxBundle\Model\PhaxReaction
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $is_phax_request = $this
                ->container
                ->get('request')
                ->request
                ->has('phax_metadata')
        ;
        
        if ($is_phax_request) {
            return $this->reaction($parameters);
        } else {
            return $this->container->get('templating')->renderResponse($view, $parameters, $response);
        }
    }
    
    
    /**
     * @param type $msg
     * @return \EL\PhaxBundle\Model\PhaxReaction
     * 
     * Build and return a valid phax error response with one message
     */
    public function error($msg)
    {
        $reaction = new PhaxReaction();
        $reaction->addError($msg);
        return $reaction;
    }
    
    
    /**
     * @return \EL\PhaxBundle\Model\PhaxReaction
     * 
     * Build and return a void phax reaction,
     * without calling js reaction function
     */
    public function void()
    {
        $reaction = new PhaxReaction();
        $reaction->disableJsReaction();
        return $reaction;
    }
    
    
    /**
     * @param string $message
     * @return \EL\PhaxBundle\Model\PhaxReaction
     * 
     * Build and return a simple message defined in metadata.
     * Usefull for display message in command line mode.
     */
    public function metaMessage($message)
    {
        $reaction = new PhaxReaction();
        $reaction->setMetaMessage($message);
        return $reaction;
    }
    
}