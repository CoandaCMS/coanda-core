<?php namespace CoandaCMS\Coanda\Core\Attributes\Types;

use Input;
use Coanda;
use CoandaCMS\Coanda\Core\Attributes\AttributeType;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

class File extends AttributeType {

    /**
     * @return string
     */
    public function identifier()
    {
        return 'file';
    }

    /**
     * @return string
     */
    public function edit_template()
    {
        return 'coanda::admin.core.attributes.edit.file';
    }

    /**
     * @return string
     */
    public function view_template()
    {
        return 'coanda::admin.core.attributes.view.file';
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
        if (isset($parameters['data_key']))
        {
            $file_key = $parameters['data_key'] . '_file';

            if (Input::hasFile($file_key))
            {
                $file = Coanda::module('media')->handleUpload(Input::file($file_key));

                return $file->id;
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