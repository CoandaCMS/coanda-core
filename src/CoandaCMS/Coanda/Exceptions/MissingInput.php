<?php namespace CoandaCMS\Coanda\Exceptions;

class MissingInput extends \Exception {

	private $missing_fields = [];

	public function __construct($missing_fields)
	{
		$this->missing_fields = $missing_fields;
	}

	public function getMissingFields()
	{
		return $this->missing_fields;
	}

}