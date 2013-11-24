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
     * Enabled by default.
     * 
     * Attention, some debugs may be uncatched if true
     */
    const ENABLED = false;
    
	
	/**
	 * Closures to call
	 * 
	 * @var array of Closure objects
	 */
	private $callbacks;
	
	
	public function __construct()
	{
		$this->clearAll();
	}
	
	/**
	 * If the service is disable, it runs tasks
	 * directly when calling addCall() function.
	 * If enabled, the service will call callbacks
	 * when calling addCall()
	 */
	public function isEnabled()
	{
		return self::ENABLED;
	}
	
	/**
	 * Add a task to do later
	 * 
	 * @param Closure $callback
	 * @param string $key (optional) unique key to identify the callback.
	 * 						if you want to override a task you added earlier,
	 * 						use the same key.
	 */
	public function addCall(\Closure $callback, $key = null)
	{
		if ($this->isEnabled()) {
			if (is_null($key)) {
				$key = uniqid();
			}
			$this->callbacks[$key] = $callback;
		} else {
			$callback();
		}
	}
    
	
	/**
	 * Remove a callback from its key.
	 * 
	 * @param string $key
	 */
	public function removeCall($key)
	{
		if (isset($this->callbacks[$key])) {
			unset($this->callbacks[$key]);
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
		
		$this->clearAll();
	}
	
	
	public function onKernelTerminate()
	{
		$this->callAll();
	}
}



