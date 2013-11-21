<?php

namespace EL\ELCoreBundle\Services;


/**
 * Service execute function AFTER kernel response.
 * Usefull for persist/flush entities,
 * or do other things which are not required for response.
 * 
 * @author alcalyn
 *
 */
class IllDoItLaterService
{
	/**
	 * @var boolean
	 */
	private $enabled;
	
	/**
	 * Closures to call
	 * 
	 * @var array of Closure objects
	 */
	private $callbacks;
	
	
	public function __construct()
	{
		$this->clearAll();
		$this->enable();
	}
	
	/**
	 * If the service is disable, it runs tasks
	 * directly when calling addCall() function.
	 * If enabled, the service will call callbacks
	 * when calling addCall()
	 */
	public function isEnabled()
	{
		return $this->enabled;
	}
	
	/**
	 * Enable the service.
	 * Note that it is enabled by default.
	 */
	public function enable()
	{
		if (!$this->isEnabled()) {
			$this->enabled = true;
		}
	}
	
	/**
	 * Disable the service.
	 * Callbacks will be called before disabling.
	 */
	public function disable()
	{
		if ($this->isEnabled()) {
			$this->callAll();
			$this->enabled = false;
		}
	}
	
	/**
	 * Add a task to do later
	 * 
	 * @param Closure $callback
	 */
	public function addCall(\Closure $callback)
	{
		if ($this->enabled) {
			$this->callbacks []= $callback;
		} else {
			$callback;
		}
	}
	
	/**
	 * Clear all the tasks. Will never be triggered.
	 */
	public function clearAll()
	{
		$this->callbacks = array();
	}
	
	/**
	 * Call all the tasks you have reported until now !
	 */
	public function callAll()
	{
		foreach ($this->callbacks as $callback) {
			$callback();
		}
		
		$this->callbacks = array();
	}
	
	
	public function onKernelTerminate()
	{
		$this->callAll();
	}
}



