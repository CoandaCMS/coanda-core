<?php namespace CoandaCMS\Coanda\Pages\PageAttributeTypes;

class HTML implements PageAttributeTypeInterface {

	public $identifier = 'html';

	public function store($attribute, $data)
	{
		// TODO
		// - Tidy up HTML
		// - Store base64 images into the media library and set data-image-id on the <img tag

		$attribute->attribute_data = $data;
		$attribute->save();
	}

	public function data($attribute)
	{
		return $attribute->attribute_data;
	}

}