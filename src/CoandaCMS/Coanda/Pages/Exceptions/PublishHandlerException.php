<?php namespace CoandaCMS\Coanda\Pages\Exceptions;

/**
 * Class PublishHandlerException
 * @package CoandaCMS\Coanda\Pages\Exceptions
 */
class PublishHandlerException extends \Exception {

    /**
     * @var array|string
     */
    private $invalid_fields = [];

    /**
     * @param string $invalid_fields
     */
    public function __construct($invalid_fields)
	{
		$this->invalid_fields = $invalid_fields;
	}

    /**
     * @return array|string
     */
    public function getInvalidFields()
	{
		return $this->invalid_fields;
	}

}