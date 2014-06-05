<?php namespace CoandaCMS\Coanda\Core\Attributes\Types;

use CoandaCMS\Coanda\Core\Attributes\AttributeType;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

use Carbon\Carbon;

class Date extends AttributeType {

    /**
     * @return string
     */
    public function identifier()
    {
        return 'date';
    }

    /**
     * @return string
     */
    public function edit_template()
    {
    	return 'coanda::admin.core.attributes.edit.date';
    }

    /**
     * @return string
     */
    public function view_template()
    {
    	return 'coanda::admin.core.attributes.view.date';
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
        $format = isset($data['format']) ? $data['format'] : false;

        if ($format)
        {
            try
            {
                $date = Carbon::createFromFormat($format, $data['date'], date_default_timezone_get());
            }
            catch (\InvalidArgumentException $exception)
            {
                throw new AttributeValidationException($name . ' is required');
            }
            catch (\ErrorException $exception)
            {
                throw new AttributeValidationException($name . ' is invalid');
            }
        }

		return json_encode($data);
	}

    /**
     * @param $data
     * @return mixed
     */
    public function data($data, $parameters = [])
	{
        if ($data !== '')
        {
            return json_decode($data, true);
        }
		
	}
}