<?php namespace CoandaCMS\Coanda\Pages\PageAttributeTypes;

class Textline implements PageAttributeTypeInterface {

	public $identifier = 'textline';

	public function store($attribute, $data)
	{
		$attribute->attribute_data = $data;
		$attribute->save();
	}

	public function data($attribute)
	{
		return $attribute->attribute_data;
	}

}