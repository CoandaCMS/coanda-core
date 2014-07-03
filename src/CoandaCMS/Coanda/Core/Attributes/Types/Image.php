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
            $file_key = $parameters['data_key'] . '_file';

            if (Input::hasFile($file_key))
            {
                $allowed_mime_types = ['jpeg', 'png', 'gif', 'bmp'];

                $file = Input::file($file_key);

                if (!in_array($file->guessExtension(), $allowed_mime_types))
                {
                    throw new AttributeValidationException($name . ' must be an image, ".' . $file->guessExtension() . '" file was uploaded');
                }

                $image = Coanda::module('media')->handleUpload(Input::file($file_key));

                return $image->id;
            }
        }

        if (isset($data['media_id']))
        {
            return $data['media_id'];
        }

		if ($is_required)
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
        $media_id = (int)$data;

        if ($media_id)
        {
            return Coanda::module('media')->getMedia($media_id);
        }

        return false;
	}

    public function render($data, $parameters = [])
    {
        // If we are indexing, then we don't want to return the media object, just an exmpty string will do
        if ($parameters['indexing'])
        {
            return '';
        }

        return $data;
    }    
}