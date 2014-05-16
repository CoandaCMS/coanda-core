<?php namespace CoandaCMS\Coanda\Core\Attributes\Types;

use Input, Coanda;

use CoandaCMS\Coanda\Core\Attributes\AttributeType;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

class Image extends AttributeType {

    /**
     * @return string
     */
    public function identifier()
	{
		return 'image';
	}

    /**
     * @return string
     */
    public function edit_template()
    {
    	return 'coanda::admin.core.attributes.edit.image';
    }

    /**
     * @return string
     */
    public function view_template()
    {
    	return 'coanda::admin.core.attributes.view.image';
    }

    /**
     * @param $data
     * @param $is_required
     * @param $name
     * @return string
     * @throws \CoandaCMS\Coanda\Exceptions\AttributeValidationException
     */
    public function store($data, $is_required, $name, $parameters = [])
	{
        if (isset($parameters['data_key']))
        {
            $file_key = $parameters['data_key'];

            if (Input::hasFile($file_key))
            {
                $image = Coanda::module('media')->handleUpload(Input::file($file_key));

                return $image->id;
            }
        }

        if (Input::has($parameters['data_key'] . '_media_id'))
        {
            return Input::get($parameters['data_key'] . '_media_id');
        }

		// - Tidy up HTML?
		if ($is_required && (!$data || $data == ''))
		{
			throw new AttributeValidationException($name . ' is required');
		}

		return false;
	}

    /**
     * @param $data
     * @return mixed
     */
    public function data($data, $parameters = [])
	{
        return Coanda::module('media')->getMedia($data);
	}
}