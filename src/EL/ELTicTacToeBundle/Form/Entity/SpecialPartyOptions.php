<?php

namespace EL\ELTicTacToeBundle\Form\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 */
class SpecialPartyOptions {
    
	
	/**
	 * @var string
	 */
	private $option_1;
    
	/**
	 * @var string
	 */
	private $option_tictactoe;
	
	/**
	 * @var boolean
	 */
	private $option_2;
	
	
	public function __construct()
	{
	}
	
	
	public function getOption1()
	{
		return $this->option_1;
	}
	
	public function getOptionTicTacToe()
	{
		return $this->option_tictactoe;
	}
	
	public function getOption2()
	{
		return $this->option_2;
	}
	
	
	public function setOption1($option1)
	{
		$this->option_1 = $option1;
		return $this;
	}
	
	public function setOptionTicTacToe($option_tictactoe)
	{
		$this->option_tictactoe = $option_tictactoe;
		return $this;
	}
	
	public function setOption2($option2)
	{
		$this->option_2 = $option2;
		return $this;
	}
	
	
}
