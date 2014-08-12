<?php

namespace EL\CoreBundle\EventListener;

use EL\CoreBundle\Services\JsVarsService;

class ConfigureClank
{
    /**
     * @var JsVarsService
     */
    private $jsVars;
    
    /**
     * @var string
     */
    private $clank_host;
    
    /**
     * @var string
     */
    private $clank_port;
    
    /**
     * @param \EL\CoreBundle\Services\JsVarsService $jsVars
     * @param string $clank_host
     * @param string $clank_port
     */
    public function __construct(JsVarsService $jsVars, $clank_host, $clank_port)
    {
        $this->jsVars = $jsVars;
        $this->clank_host = $clank_host;
        $this->clank_port = $clank_port;
    }
    
    /**
     * Add Clank configuration in js context
     */
    public function init()
    {
        $clank = array(
            'host' => $this->clank_host,
            'port' => $this->clank_port,
        );
        
        $this->jsVars->addContext('clank', $clank);
    }
    
    /**
     * Init the configuration on kernel controller
     */
    public function onKernelController()
    {
        $this->init();
    }
}
