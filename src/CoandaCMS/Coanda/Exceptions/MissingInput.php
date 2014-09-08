<?php namespace CoandaCMS\Coanda\Exceptions;

/**
 * Class MissingInput
 * @package CoandaCMS\Coanda\Exceptions
 */
class MissingInput extends \Exception {

    /**
     * @var array
     */
    private $missing_fields = [];

    /**
     * @param string $missing_fields
     */
    public function __construct(array $missing_fields = [])
	{
		$this->missing_fields = $missing_fields;
	}

    /**
     * @return array|string
     */
    public function getMissingFields()
	{
		return $this->missing_fields;
	}

}