<?php namespace CoandaCMS\Coanda\Pages\PageAttributeTypes;

use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

class HTML implements PageAttributeTypeInterface {

	public $identifier = 'html';

	public function store($attribute, $data)
	{
		if ($data == '<p><br></p>')
		{
			$data = '';
		}

		// TODO
		// - Tidy up HTML
		// - Store base64 images into the media library and set data-image-id on the <img tag
		if ($attribute->is_required && (!$data || $data == ''))
		{
			throw new AttributeValidationException($attribute->name . ' is required');
		}

		$attribute->attribute_data = $data;
		$attribute->save();
	}

	public function data($attribute)
	{
		return $attribute->attribute_data;
	}

}