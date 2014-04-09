<?php namespace CoandaCMS\Coanda\Pages\Exceptions;

class PublishHandlerException extends \Exception {

	private $invalid_fields = [];

	public function __construct($invalid_fields)
	{
		$this->invalid_fields = $invalid_fields;
	}

	public function getInvalidFields()
	{
		return $this->invalid_fields;
	}

}