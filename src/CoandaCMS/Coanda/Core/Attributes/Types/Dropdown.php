<?php namespace CoandaCMS\Coanda\Core\Attributes\Types;

use CoandaCMS\Coanda\Core\Attributes\AttributeType;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

class Dropdown extends AttributeType {

    /**
     * @return string
     */
    public function identifier()
    {
        return 'dropdown';
    }

    /**
     * @return string
     */
    public function edit_template()
    {
    	return 'coanda::admin.core.attributes.edit.dropdown';
    }

    /**
     * @return string
     */
    public function view_template()
    {
    	return 'coanda::admin.core.attributes.view.dropdown';
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
}