<?php namespace CoandaCMS\Coanda\Exceptions;

/**
 * Class AttributeValidationException
 * @package CoandaCMS\Coanda\Exceptions
 */
class AttributeValidationException extends \Exception { 

    private $validation_data = [];

    public function __construct($validation_data)
	{
		$this->validation_data = $validation_data;
	}

    /**
     * @return array|string
     */
    public function getValidationData()
	{
		return $this->validation_data;
	}

}