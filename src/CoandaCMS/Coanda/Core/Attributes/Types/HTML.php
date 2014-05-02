<?php namespace CoandaCMS\Coanda\Core\Attributes\Types;

use CoandaCMS\Coanda\Core\Attributes\AttributeTypeInterface;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

class HTML implements AttributeTypeInterface {

	public function identifier()
	{
		return 'html';
	}

    public function edit_template()
    {
    	return 'coanda::admin.core.attributes.edit.html';
    }

    public function view_template()
    {
    	return 'coanda::admin.core.attributes.view.html';
    }

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

    public function data($data)
	{
		// TODO - tidy up the HTML ready for displaying...
		return $data;
	}
}