<?php namespace CoandaCMS\Coanda\Pages\PageAttributeTypes;

use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

class Textline implements PageAttributeTypeInterface {

	public $identifier = 'textline';

	public function store($attribute, $data)
	{
		// Is this required?
		if ($attribute->is_required && (!$data || $data == ''))
		{
			throw new AttributeValidationException;
		}

		$attribute->attribute_data = $data;
		$attribute->save();
	}

	public function data($attribute)
	{
		return $attribute->attribute_data;
	}

}