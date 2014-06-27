<?php namespace CoandaCMS\Coanda\Core\Attributes\Types;

use CoandaCMS\Coanda\Core\Attributes\AttributeType;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

use Carbon\Carbon;
use Config;

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
        if (!isset($data['format']))
        {
            $data['format'] = Config::get('coanda::coanda.date_format');
        }

        if ($data['format'])
        {
            try
            {
                Carbon::createFromFormat($data['format'], $data['date'], date_default_timezone_get());
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

    public function render($data, $parameters = [])
    {
        $date = Carbon::createFromFormat($data['format'], $data['date'], date_default_timezone_get());
        
        return $date->format(Config::get('coanda::coanda.date_format'));
    }
}