<?php namespace CoandaCMS\Coanda\Pages\PageAttributeTypes;

use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

/**
 * Class Textline
 * @package CoandaCMS\Coanda\Pages\PageAttributeTypes
 */
class Textline implements PageAttributeTypeInterface {

    /**
     * @var string
     */
    public $identifier = 'textline';

    /**
     * @param Attribute $attribute
     * @param Array $data
     * @throws \CoandaCMS\Coanda\Exceptions\AttributeValidationException
     */
    public function store($attribute, $data)
	{
		// Is this required?
		if ($attribute->is_required && (!$data || $data == ''))
		{
			throw new AttributeValidationException($attribute->name . ' is required');
		}

		$attribute->attribute_data = $data;
		$attribute->save();
	}

    /**
     * @param Attribute $attribute
     * @return mixed
     */
    public function data($attribute)
	{
		return $attribute->attribute_data;
	}

}