<?php

namespace EL\ELCoreBundle\Services;

use Symfony\Component\Routing\Router;
use EL\ELCoreBundle\Services\SessionService;
use Symfony\Component\Translation\Translator;


class JsVarsService
{
	
	const TYPE_PHAX_CONFIG				= 'phax-config';
	const TYPE_PHAX_LOAD_CONTROLLERS	= 'phax-load-controllers';
	const TYPE_JS_CONTEXT				= 'js-context';
	const TYPE_TRANSLATION				= 'translations';
	
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
	private $session_service;
	
	/**
	 * @var Translator
	 */
	private $translator;
	
	
	
	
	public function __construct(Router $router, SessionService $session_service, Translator $translator)
	{
		$this->router			= $router;
		$this->session_service	= $session_service;
		$this->translator		= $translator;
		
		$this->vars = array(
			self::TYPE_PHAX_CONFIG				=> array(),
			self::TYPE_PHAX_LOAD_CONTROLLERS	=> array(),
			self::TYPE_JS_CONTEXT				=> array(),
			self::TYPE_TRANSLATION				=> array(),
		);
		
		$this->initVars();
	}
	
	
	private function initVars()
	{
		$this->set(self::TYPE_PHAX_CONFIG, 'www_script', $this->router->generate('phax_script', array()));
		$this->set(self::TYPE_PHAX_CONFIG, 'www_root', $this->router->generate('elcore_home', array()));
		//$this->initPhaxController('surf');
		$this->addContext('player', $this->session_service->getPlayer()->jsonSerialize());
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
		} else if (is_null($key)) {
			return $this->vars[$type];
		} else {
			return $this->vars[$type][$key];
		}
	}
	
	
	public function getAll()
	{
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
