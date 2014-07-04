<?php namespace CoandaCMS\Coanda\Core\Attributes\Types;

use CoandaCMS\Coanda\Core\Attributes\AttributeType;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

class Integer extends AttributeType {

    /**
     * @return string
     */
    public function identifier()
    {
        return 'integer';
    }

    /**
     * @return string
     */
    public function edit_template()
    {
    	return 'coanda::admin.core.attributes.edit.integer';
    }

    /**
     * @return string
     */
    public function view_template()
    {
    	return 'coanda::admin.core.attributes.view.integer';
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
        if ($is_required && (!$data || $data == ''))
        {
            throw new AttributeValidationException($name . ' is required');
        }

        if ($data && $data !== '')
        {
            if (!is_numeric($data))
            {
                throw new AttributeValidationException($name . ' must be a number');
            }
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