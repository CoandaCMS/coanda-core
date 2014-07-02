<?php namespace CoandaCMS\Coanda\Core\Attributes\Types;

use CoandaCMS\Coanda\Core\Attributes\AttributeType;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

class Options extends AttributeType {

    /**
     * @return string
     */
    public function identifier()
    {
        return 'options';
    }

    /**
     * @return string
     */
    public function edit_template()
    {
    	return 'coanda::admin.core.attributes.edit.options';
    }

    /**
     * @return string
     */
    public function view_template()
    {
    	return 'coanda::admin.core.attributes.view.options';
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