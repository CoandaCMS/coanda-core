<?php namespace CoandaCMS\Coanda\Core\Attributes\Types;

use CoandaCMS\Coanda\Core\Attributes\AttributeTypeInterface;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

/**
 * Class HTML
 * @package CoandaCMS\Coanda\Core\Attributes\Types
 */
class HTML implements AttributeTypeInterface {

    /**
     * @return string
     */
    public function identifier()
	{
		return 'html';
	}

    /**
     * @return string
     */
    public function edit_template()
    {
    	return 'coanda::admin.core.attributes.edit.html';
    }

    /**
     * @return string
     */
    public function view_template()
    {
    	return 'coanda::admin.core.attributes.view.html';
    }

    /**
     * @param $data
     * @param $is_required
     * @param $name
     * @return string
     * @throws \CoandaCMS\Coanda\Exceptions\AttributeValidationException
     */
    public function store($data, $is_required, $name)
	{
		if ($data == '<p><br></p>')
		{
			$data = '';
		}

		// TODO
		// - Tidy up HTML?
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
    public function data($data)
	{
		// TODO - tidy up the HTML ready for displaying...
		return $data;
	}
}