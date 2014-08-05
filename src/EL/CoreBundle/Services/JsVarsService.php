<?php

namespace EL\CoreBundle\Services;

use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\Translator;
use EL\CoreBundle\Services\SessionService;

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
    private $session;
    
    /**
     * @var Translator
     */
    private $translator;
    
    
    
    
    public function __construct(Router $router, SessionService $session, Translator $translator)
    {
        $this->router       = $router;
        $this->session      = $session;
        $this->translator   = $translator;
        
        $this->vars = array(
            self::TYPE_PHAX_CONFIG              => array(),
            self::TYPE_PHAX_LOAD_CONTROLLERS    => array(),
            self::TYPE_JS_CONTEXT               => array(),
            self::TYPE_TRANSLATION              => array(),
        );
    }
    
    
    private function initVars()
    {
        $this->set(self::TYPE_PHAX_CONFIG, 'www_script', $this->router->generate('phax_script', array()));
        $this->set(self::TYPE_PHAX_CONFIG, 'www_root', $this->router->generate('elcore_home', array()));
        
        $this->initPhaxController('widget');
        
        $this->addContext('player', $this->session->getPlayer()->jsonSerialize());
        $this->addContext('locale', $this->translator->getLocale());
        
        $this->useTrans('close');
    }
    
    
    /**
     * @param string $type
     */
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
    
    
    /**
     * @param string $key
     */
    public function addContext($key, $value)
    {
        $this->set(self::TYPE_JS_CONTEXT, $key, $value);
        return $this;
    }
    
    
    /**
     * @param string $controller
     */
    public function initPhaxController($controller)
    {
        $this->vars[self::TYPE_PHAX_LOAD_CONTROLLERS] []= $controller;
        return $this;
    }
    
    
    /**
     * @param string $s
     */
    public function useTrans($s)
    {
        if (is_array($s)) {
            foreach ($s as $t) {
                $this->useTrans($t);
            }
        } else {
            $this->set(self::TYPE_TRANSLATION, $s, $this->translator->trans(/** @ignore */ $s));
        }
        
        return $this;
    }
}
