<?php namespace CoandaCMS\Coanda\Exceptions;

/**
 * Class ValidationException
 * @package CoandaCMS\Coanda\Exceptions
 */
class ValidationException extends \Exception {

    /**
     * @var array|string
     */
    private $invalid_fields = [];

    private $invalid_identifier;

    /**
     * @param string $invalid_fields
     */
    public function __construct($invalid_fields, $invalid_identifier = false)
	{
        if (is_array($invalid_fields))
        {
            $this->invalid_fields = $invalid_fields;    
        }

        $this->invalid_identifier = $invalid_identifier;
	}

    /**
     * @return array
     */
    public function getInvalidFields()
	{
		return $this->invalid_fields;
	}

    /**
     * @return array|string
     */
    public function getInvalidIdentifier()
    {
        return $this->invalid_identifier;
    }
}