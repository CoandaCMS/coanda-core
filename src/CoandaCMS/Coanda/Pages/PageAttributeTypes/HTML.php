<?php namespace CoandaCMS\Coanda\Pages\PageAttributeTypes;

use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

/**
 * Class HTML
 * @package CoandaCMS\Coanda\Pages\PageAttributeTypes
 */
class HTML implements PageAttributeTypeInterface {

    /**
     * @var string
     */
    public $identifier = 'html';

    /**
     * @param Attribute $attribute
     * @param Array $data
     * @throws \CoandaCMS\Coanda\Exceptions\AttributeValidationException
     */
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

    /**
     * @param Attribute $attribute
     * @return mixed
     */
    public function data($attribute)
	{
		return $attribute->attribute_data;
	}

}