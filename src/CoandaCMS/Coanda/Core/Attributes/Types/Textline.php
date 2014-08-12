<?php namespace CoandaCMS\Coanda\Core\Attributes\Types;

use CoandaCMS\Coanda\Core\Attributes\AttributeType;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

/**
 * Class Textline
 * @package CoandaCMS\Coanda\Core\Attributes\Types
 */
class Textline extends AttributeType {

    /**
     * @return string
     */
    public function identifier()
    {
        return 'textline';
    }

    /**
     * @return string
     */
    public function edit_template()
    {
    	return 'coanda::admin.core.attributes.edit.textline';
    }

    /**
     * @return string
     */
    public function view_template()
    {
    	return 'coanda::admin.core.attributes.view.textline';
    }

    /**
     * @param $data
     * @param $is_required
     * @param $name
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\AttributeValidationException
     */
    public function store($data, $is_required, $name, $parameters = [])
	{
		// Is this required?
		if ($is_required && (!$data || $data == ''))
		{
			throw new AttributeValidationException($name . ' is required');
		}

		return $data;
	}

    /**
     * @param $data
     * @return mixed
     */
    public function data($data, $parameters = [])
	{
		return $data;
	}

    public function render($data, $parameters = [])
    {
        return htmlspecialchars($data);
    }
}