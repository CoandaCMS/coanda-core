<?php namespace CoandaCMS\Coanda\Pages\Exceptions;

/**
 * Class PublishHandlerException
 * @package CoandaCMS\Coanda\Pages\Exceptions
 */
class PublishHandlerException extends \Exception {

    /**
     * @var array
     */
    private $invalid_fields = [];

    /**
     * @param array $invalid_fields
     */
    public function __construct(array $invalid_fields)
	{
        if (is_array($invalid_fields))
        {
            $this->invalid_fields = $invalid_fields;    
        }
	}

    /**
     * @return array
     */
    public function getInvalidFields()
	{
		return $this->invalid_fields;
	}

}