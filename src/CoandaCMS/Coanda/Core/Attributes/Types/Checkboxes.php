<?php namespace CoandaCMS\Coanda\Core\Attributes\Types;

use CoandaCMS\Coanda\Core\Attributes\AttributeType;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

class Checkboxes extends AttributeType {

    /**
     * @return string
     */
    public function identifier()
    {
        return 'checkboxes';
    }

    /**
     * @return string
     */
    public function edit_template()
    {
    	return 'coanda::admin.core.attributes.edit.checkboxes';
    }

    /**
     * @return string
     */
    public function view_template()
    {
    	return 'coanda::admin.core.attributes.view.checkboxes';
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
		if ($is_required && (!$data || count($data) == 0))
		{
			throw new AttributeValidationException($name . ' is required');
		}

        return json_encode($data);
	}

    /**
     * @param $data
     * @return mixed
     */
    public function data($data, $parameters = [])
	{
        $chosen_options = json_decode($data, true);

        if (!is_array($chosen_options))
        {
            $chosen_options = [];
        }
        
        return $chosen_options;
	}
}