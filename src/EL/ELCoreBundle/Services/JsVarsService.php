<?php

namespace EL\ELCoreBundle\Services;

use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\Translator;
use EL\ELCoreBundle\Services\SessionService;

class JsVarsService
{
    const TYPE_PHAX_CONFIG              = 'phax-config';
    const TYPE_PHAX_LOAD_CONTROLLERS    = 'phax-load-controllers';
    const TYPE_JS_CONTEXT               = 'js-context';
    const TYPE_TRANSLATION              = 'translations';
    
    /**
     * Array containing variables to sent to js
     * 
     * @var array
     */
    private $vars;
    
    /**
     * @var Router
     */
    private $router;
    
    /**
     * @var SessionService
     */
    private $security_context;
    
    /**
     * @var Translator
     */
    private $translator;
    
    
    
    
    public function __construct(Router $router, $security_context, Translator $translator)
    {
        $this->router            = $router;
        $this->security_context    = $security_context;
        $this->translator        = $translator;
        
        $this->vars = array(
            self::TYPE_PHAX_CONFIG                => array(),
            self::TYPE_PHAX_LOAD_CONTROLLERS    => array(),
            self::TYPE_JS_CONTEXT                => array(),
            self::TYPE_TRANSLATION                => array(),
        );
    }
    
    
    private function initVars()
    {
        $this->set(self::TYPE_PHAX_CONFIG, 'www_script', $this->router->generate('phax_script', array()));
        $this->set(self::TYPE_PHAX_CONFIG, 'www_root', $this->router->generate('elcore_home', array()));
        
        //$this->initPhaxController('surf');
        $this->initPhaxController('widget');
        
        $this->addContext('player', $this->security_context->getToken()->getUser()->jsonSerialize());
        $this->addContext('locale', $this->translator->getLocale());
    }
    
    
    public function set($type, $key, $value)
    {
        $this->vars[$type][$key] = $value;
        return $this;
    }
    
    
    public function get($type = null, $key = null)
    {
        if (is_null($type)) {
            return $this->vars;
        } elseif (is_null($key)) {
            return $this->vars[$type];
        } else {
            return $this->vars[$type][$key];
        }
    }
    
    
    public function getAll()
    {
        $this->initVars();
        return $this->get();
    }
    
    public function getExport()
    {
        $this->initVars();
        return $this->get();
    }
    
    
    public function addContext($key, $value)
    {
        $this->set(self::TYPE_JS_CONTEXT, $key, $value);
        return $this;
    }
    
    
    public function initPhaxController($controller)
    {
        $this->vars[self::TYPE_PHAX_LOAD_CONTROLLERS] []= $controller;
        return $this;
    }
    
    
    public function useTrans($s)
    {
        $this->set(self::TYPE_TRANSLATION, $s, $this->translator->trans(/** @Ignore */ $s));
        return $this;
    }
}
