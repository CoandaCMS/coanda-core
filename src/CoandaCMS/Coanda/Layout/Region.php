<?php namespace CoandaCMS\Coanda\Layout;

class Region {

	public $identifier;
	public $name;

	public function __construct($identifier, $name)
	{
		$this->name = $name;
		$this->identifier = $identifier;
	}

	public function identifier()
	{
		return $this->identifier;
	}
	
	public function name()
	{
		return $this->name;
	}

}
