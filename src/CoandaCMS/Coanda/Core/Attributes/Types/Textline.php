<?php namespace CoandaCMS\Coanda\Core\Attributes\Types;

use CoandaCMS\Coanda\Core\Attributes\AttributeTypeInterface;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

class Textline implements AttributeTypeInterface {

    public function identifier()
    {
        return 'textline';
    }

    public function store($data, $is_required, $name)
	{
		// Is this required?
		if ($is_required && (!$data || $data == ''))
		{
			throw new AttributeValidationException($name . ' is required');
		}

		return $data;
	}

    public function data($data)
	{
		return $data;
	}

}