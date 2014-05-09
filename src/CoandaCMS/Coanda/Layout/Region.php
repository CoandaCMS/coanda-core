<?php namespace CoandaCMS\Coanda\Layout;

/**
 * Class Region
 * @package CoandaCMS\Coanda\Layout
 */
class Region {

    /**
     * @var
     */
    public $identifier;
    /**
     * @var
     */
    public $name;

    /**
     * @param $identifier
     * @param $name
     */
    public function __construct($identifier, $name)
	{
		$this->name = $name;
		$this->identifier = $identifier;
	}

    /**
     * @return mixed
     */
    public function identifier()
	{
		return $this->identifier;
	}

    /**
     * @return mixed
     */
    public function name()
	{
		return $this->name;
	}

}
